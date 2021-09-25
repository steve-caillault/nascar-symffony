<?php

/**
 * Tests du contrôleur de recherche par autocomplètion d'une ville
 */

namespace App\Tests\Controllers\Admin\State\Country\CountryState\City;

use Symfony\Component\DomCrawler\Crawler;
/***/
use App\Tests\WithUserCreating;
use App\Tests\Controllers\Admin\State\{
    WithCountryCreation,
    WithCountryStateCreation
};
use App\Tests\BaseTestCase;
use App\Entity\{
    User,
    City,
    CountryState
};

use App\DataFixtures\CityFixtures;

final class AutocompleteSearchingTest extends BaseTestCase {

    use WithUserCreating;

    /**
     * Setup
     * @return void
     */
    /*protected function setUp() : void
    {
        parent::setUp();
        $this->executeFixtures([ CityFixtures::class ]);
    }*/

    /**
     * Retourne la liste des villes qu'on devrait obtenir pour la recherche
     * @param ?string $searching Terme de la recherche
     */
    private function getCityListExpected(?string $searching)
    {
        $cityFixtures = $this->getService(CityFixtures::class);
        $data = $cityFixtures->getDataFromCSV();

        // Tri les villes
        usort($data, function($city1, $city2) {
            $cityName1 = $city1['name'];
            $cityName2 = $city2['name'];
            if($cityName1 === $cityName2)
            {
                return ($city1['id'] < $city2['id']) ? -1 : 1;
            }
            return ($cityName1 < $cityName2 ? -1 : 1);
        });

        // Filtre les villes correspondant à la recherche
        if($searching !== null)
        {
            $data = array_filter($data, fn($item) => str_contains($item['name'], $searching));
        }

        return $data;
    }

    /**
     * Tentative d'appel Ajax
     * @param array $params Paramètres à transmettre en POST
     * @param array $expectedData Données que l'appel la réponse doit envoyer
     * @return void
     */
    private function attemptCalling(array $params, array $expectedData) : void
    {
        $user = $this->userToLogged();

        $client = $this->getHttpClient();
        
        $client->loginUser($user, 'admin');
        $client->xmlHttpRequest('POST', '/admin/ajax/entity/searching', $params);
        
        $responseExpected = json_encode([
            'status' => 'SUCCESS',
            'data' => $expectedData,
        ]);
        $responseContent = $client->getResponse()->getContent();
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($responseExpected, $responseContent);
    }

    /**
     * Test de succès
     * @param array $params Paramètres à transmettre en POST
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $this->executeFixtures([ CityFixtures::class ]);

        $searching = $params['searching'];

        $expectedData = [];

        $searchingResultsExpected = $this->getCityListExpected($searching);
        foreach($searchingResultsExpected as $result)
        {
            $expectedData[] = [
                'value' => $result['id'],
                'text' => $result['name'] . ' - ' . $result['stateName'],
            ];
        }

        $this->attemptCalling($params, $expectedData);
    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        return array(
            [
                // Pas de ville correspondante
                [
                    'class' => City::class,
                    'searching' => 'azerty',
                ],
            ],
            [
                // Plusieurs villes
                [
                    'class' => City::class,
                    'searching' => 'ond',
                ],
            ],
        );
    }

    /**
     * Tests de l'échec de la validation
     * @param array $params Paramètres à transmettre en POST
     * @dataProvider failureValidationProvider
     * @return void
     */
    public function testValidationFailure(array $params) : void
    {
        $this->attemptCalling($params, []);
    }
        
    /**
     * Provider pour les tests d'échec de la validation
     * @return array
     */
    public function failureValidationProvider() : array
    {
        return array(
            [
                // Test paramètres vide
                [],
            ],
            [
                // Chaines vides pour les paramètres
                [
                    'class' => '',
                    'searching' => '',
                ]
            ],
            [
                // Classe PHP n'existe pas
                [
                    'class' => 'App\Fruit',
                    'searching' => 'Tomate',
                ],
            ],
            [
                // La classe n'implèmente pas AutocompleteEntityInterface
                // On ne peut pas tester que le Repository implèmente SearchingRepositoryInterface,
                // il faudrait pour cela créer une entité qui implèment AutocompleteEntityInterface,
                // mais ne pas faire la gestion de son repository
                [
                    'class' => User::class,
                    'searching' => 'Arthur',
                ]
            ],
            [
                // La recherche est trop courte
                [
                    'class' => City::class,
                    'searching' => 'po',
                ]
            ],
            [
                // La recherche est trop longue
                [
                    'class' => City::class,
                    'searching' => $this->getFaker()->realTextBetween(),
                ]
            ]
        );
    }

}
