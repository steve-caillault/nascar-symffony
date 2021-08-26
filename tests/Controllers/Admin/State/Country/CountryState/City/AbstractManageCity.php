<?php

/**
 * Tests du contrôleur d'ajout d'une ville
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
    City,
    CountryState
};

abstract class AbstractManageCity extends BaseTestCase {

    use WithUserCreating, WithCountryCreation, WithCountryStateCreation;

    /**
     * Compte le nombre de villes
     * @return int
     */
    protected function countCities() : int
    {
        $dql = strtr('SELECT COUNT(cities.id) FROM :object cities', [
            ':object' => City::class,
        ]);

        return $this->getEntityManager()->createQuery($dql)->getSingleScalarResult();
    }

    /**
     * Retourne la dernière ville qui a été créée
     * @return ?City
     */
    private function getLatestCity() : ?City
    {
        $dql = strtr('SELECT cities FROM :object cities ORDER BY cities.id DESC', [
            ':object' => City::class,
        ]);

        return $this->getEntityManager()->createQuery($dql)->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Retourne la ville en fonction du nom en paramètre
     * @return ?City
     */
    protected function getCityByName(string $name) : ?City
    {
        $dql = strtr('SELECT cities FROM :object cities WHERE cities.name = :name', [
            ':object' => City::class,
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    /**
     * Création d'une ville
     * @param CountryState $state
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     * @return void
     */
    protected function createCity(
        CountryState $state,
        string $name,
        float $latitude,
        float $longitude
    ) : void
    {
        $city = (new City())
            ->setName($name)
            ->setState($state)
            ->setLatitude($latitude)
            ->setLongitude($longitude);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($city);
        $entityManager->flush();
    }

    /**
     * Retourne l'URI de la page de gestion de la ville
     * @return string
     */
    abstract protected function manageCityUri() : string;

    /**
     * Retourne le modèle du message de succès 
     * @return string
     */
    abstract protected function getSuccessMessageTemplate() : string;

    /**
     * Retourne le titre de la page attendu en cas d'échec
     * @return string
     */
    abstract protected function getFailureExpectedPageTitle() : string;

    /**
     * Tentative de gestion d'une ville
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    protected function attemptManageCity(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $this->manageCityUri());

        try {
            $submitButton = $crawler->filter('form[name=city] input[type=submit]');
            $formSeason = $submitButton->form();

            $postParams = [];
            foreach($params as $key => $value)
            {
                $fieldKey = 'city[' . $key . ']';
                $postParams[$fieldKey] = $value;
            }

            return $client->submit($formSeason, $postParams);
        } catch(\Throwable) {
            return $crawler;
        }
    }

    /*****************************************************************************/

    /**
     * Vérification du succès de la création d'une ville
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $response = $this->attemptManageCity($params);

        $expectedCityName = $params['name'];

        // Vérification du message Flash
        $expectedFlashMessage = strtr($this->getSuccessMessageTemplate(), [
            ':name' => $expectedCityName,
        ]);
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des villes de l\'état Iowa');
        $this->assertPageTitleSame('Liste des villes de l\'état Iowa');
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $managedCity = $this->getCityByName($expectedCityName);
        $this->assertNotNull($managedCity);

        // Vérification des données
        $expectedData = [
            'state' => 'IA',
            'name' => $params['name'],
        ];
        $resultData = [
            'state' => $managedCity?->getState()?->getCode(),
            'name' => $managedCity?->getName(),
        ];

        $this->assertEquals($expectedData, $resultData);

        // Cas spécifique des coordonnées qui sont stockées avec des arrondis
        $this->assertEqualsWithDelta($params['latitude'], $managedCity?->getLatitude(), 0.001);
        $this->assertEqualsWithDelta($params['longitude'], $managedCity?->getLongitude(), 0.001);
    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        $faker = $this->getFaker();
        $getFakerParams = function() use ($faker) {
            return [ 
                'name' => $faker->city(),
                'latitude' => $faker->latitude(),
                'longitude' => $faker->longitude(),
            ];
        };

        return array(
            // Test 1
            [
                $getFakerParams(),
            ],
            // Test 2
            [
                $getFakerParams(),
            ],
            // Test 3
            [
                $getFakerParams(),
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Vérification des erreurs lors de la gestion d'une ville
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $latestCity = $this->getLatestCity();

        $response = $this->attemptManageCity($params);

        $titleExpected = $this->getFailureExpectedPageTitle();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);

        // Vérifie qu'aucune ville n'a été ajouté
        $this->assertEquals($latestCity?->getId(), $this->getLatestCity()?->getId());

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = 'city[' . $field . ']';
            $fieldSelector = '[name="' . $fieldKey . '"]';
            $error = $response->filter($fieldSelector)->closest('div.form-input')->filter('p.error')->text();
            $this->assertEquals($messageExpected, $error);
        }
    }

    /**
     * Provider pour les tests d'échec de la validation
     * @return array
     */
    public function failureValidationProvider() : array
    {
        $faker = $this->getFaker();
        return array(
            'empty' => [
                // Test paramètres vide
                [
                    'name' => '',
                    'latitude' => '',
                    'longitude' => '',
                ], [
                    'name' => 'Le nom de la ville est nécessaire.',
                    'latitude' => 'La latitude est nécessaire.',
                    'longitude' => 'La longitude est nécessaire.',
                ],
            ],
            'name_too_short' => [
                // Test lorsque le nom est trop court
                [
                    'name' => 'pom',
                    'latitude' => $faker->latitude(),
                    'longitude' => $faker->longitude(),
                ], [
                    'name' => 'Le nom doit avoir au moins 4 caractères.',
                ],
            ],
            'name_too_long' => [
                // Test lorsque le nom est trop long
                [
                    'name' => $faker->realTextBetween(51, 100),
                    'latitude' => $faker->latitude(),
                    'longitude' => $faker->longitude(),
                ], [
                    'name' => 'Le nom ne doit pas avoir plus de 50 caractères.',
                ],
            ],
            'incorrect_latitude' => [
                // Test lorsque la latitude est incorrecte
                [
                    'name' => $faker->name(),
                    'latitude' => 'a',
                    'longitude' => $faker->longitude(),
                ], [
                    'latitude' => 'La latitude doit être un nombre décimal.',
                ],
            ],
            'incorrect_longitude' => [
                // Test lorsque la longitude est incorrect
                [
                    'name' => $faker->name(),
                    'latitude' => $faker->latitude(),
                    'longitude' => 'bc',
                ], [
                    'longitude' => 'La longitude doit être un nombre décimal.',
                ],
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Appel lorsque l'adresse de l'appel n'existe pas
     * @return void
     */
    protected function checkNotFoundCityCalling() : void
    {
        $latestCity = $this->getLatestCity();

        $this->attemptManageCity([]);

        $expectedTitle = 'Erreur 404';
        
        $this->assertResponseStatusCodeSame(404);
        $this->assertSelectorTextContains('h1', $expectedTitle);
        $this->assertPageTitleSame($expectedTitle);

        $this->assertEquals($latestCity, $this->getLatestCity());
    }

    /**
     * Test si le pays n'existe pas
     * @return void
     */
    public function testCountryNotExists() : void
    {
        $this->checkNotFoundCityCalling();
    }

    /**
     * Test si l'état n'existe pas
     * @return void
     */
    public function testCountryStateNotExists() : void
    {
        $this->createCountry('US', 'États-Unis d\'Amérique');
        $this->checkNotFoundCityCalling();
    }

    /**
     * Test si l'état n'appartient pas au pays
     * @return void
     */
    public function testCountryStateNotBelongsCountry() : void
    {
        $usCountry = $this->createCountry('US', 'États-Unis d\'Amérique');
        $canadaCountry = $this->createCountry('CA', 'Canada');

        // Aberration, mais nécessaire pour le test
        $this->createCountryState($canadaCountry, 'IA', 'Iowa');

        $this->testCountryNotExists();
    }

    
   

}