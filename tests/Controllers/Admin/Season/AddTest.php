<?php

/**
 * Tests du contrôleur d'ajout d'une saison
 */

namespace App\Tests\Controllers\Admin\Season;

use Symfony\Component\DomCrawler\Crawler;
/***/
use App\Tests\WithUserCreating;
use App\Tests\BaseTestCase;
use App\Entity\Season;

final class AddTest extends BaseTestCase {

    use WithUserCreating;

    /**
     * Création de la saison counrante
     * @return void
     */
    private function createCurrentSeason() : void
    {
        $season = (new Season())->setYear(date('Y'))->setState(Season::STATE_CURRENT);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($season);
        $entityManager->flush();
    }

    /**
     * Retourne la dernière saison qui a été créée
     * @return ?Season
     */
    private function getLatestSeason() : ?Season
    {
        $dql = strtr('SELECT seasons FROM :object seasons ORDER BY seasons.id DESC', [
            ':object' => Season::class,
        ]);

        return $this->getEntityManager()->createQuery($dql)->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Tentative de création d'une saison
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    private function attemptSeasonCreation(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', '/admin/seasons/add');
      
       // $crawler->filter('input[name="season[state]"]')->disableValidation();

        $submitButton = $crawler->filter('form#admin-seasons-add input[type=submit]');
        $formSeason = $submitButton->form();

        $formSeason['season[state]']->disableValidation();

        $postParams = [];
        foreach($params as $key => $value)
        {
            $fieldKey = 'season[' . $key . ']';
            $postParams[$fieldKey] = $value;
        }

        return $client->submit($formSeason, $postParams);
    }

    /*****************************************************************************/

    /**
     * Vérification du succès de la création d'une saison
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $response = $this->attemptSeasonCreation($params);

        $expectedSeasonYear = $params['year'];

        // Vérification du message Flash
        $expectedFlashMessage = strtr('La saison :year a été créée.', [
            ':year' => $expectedSeasonYear,
        ]);
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des saisons');
        $this->assertPageTitleSame('Liste des saisons');
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $lastSeason = $this->getLatestSeason();
        $this->assertNotNull($lastSeason);

        // Vérification des données
        $expectedData = [
            'year' => $params['year'],
            'state' => $params['state'],
        ];
        $resultData = [
            'year' => $lastSeason?->getYear(),
            'state' => $lastSeason?->getState(),
        ];

        $this->assertEquals($expectedData, $resultData);

    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        $faker = $this->getFaker();
        return array(
            // Saison courante
            [
                [ 
                    'year' => date('Y'),
                    'state' => 'CURRENT',
                ],
            ],
            // Saison active
            [
                [
                    'year' => date('Y') - 1,
                    'state' => 'ACTIVE',
                ]
            ],
            // Saison désactivée
            [
                [
                    'year' => date('Y') - 2,
                    'state' => 'DISABLED',
                ]
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected)
    {
        $this->createCurrentSeason();

        $latestSeason = $this->getLatestSeason();

        $response = $this->attemptSeasonCreation($params);
        $newLatestSeason = $this->getLatestSeason();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', 'Création d\'une saison');
        $this->assertPageTitleSame('Création d\'une saison');

        // Vérifie qu'aucune saison n'a été ajouté
        $this->assertEquals($latestSeason, $newLatestSeason);

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = 'season[' . $field . ']';
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

        
        //dd($latestSeason);

        return array(
            [
                // Test paramètres
                [], [
                    'year' => 'L\'année de la saison est nécessaire.',
                    'state' => 'Le statut de la saison est nécessaire.',
                ],
            ],
            [
                // Test d'une saison existante
                [
                    'year' => date('Y'),
                    'state' => 'ACTIVE',
                ], [
                    'year' => strtr('La saison :year existe déjà.', [ ':year' => date('Y') ]),
                ],
            ],
            [
                // Test lorsque l'année n'est pas un nombre
                [
                    'year' => 'pom',
                    'state' => 'DISABLED',
                ], [
                    'year' => 'L\'année doit être un nombre positif.',
                ],
            ],
            [
                // Test lorsque l'année n'a pas quatre chiffres
                [
                    'year' => 25,
                    'state' => 'ACTIVE',
                ], [
                    'year' => 'L\'année doit être composée de quatre chiffres.',
                ],
            ],
            [
                // Test lorsque l'état est incorrect
                [
                    'year' => date('Y') - 1,
                    'state' => 'ACTIVED',
                ], [
                    'state' => 'Le statut de la saison est incorrect.',
                ],
            ],
            [
                // Test lorsque l'état CURRENT est déjà attribué
                [
                    'state' => 'CURRENT',
                ], [
                    'state' => strtr('La saison actuelle est attribuée à la saison :year.', [ ':year' => date('Y'), ]),
                ]
            ]
        );
    }

    /*****************************************************************************/

}