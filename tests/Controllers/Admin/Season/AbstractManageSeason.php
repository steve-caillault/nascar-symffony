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

abstract class AbstractManageSeason extends BaseTestCase {

    use WithUserCreating;

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
     * Création d'une saison
     * @param int $year
     * @param string $state
     * @return void
     */
    protected function createSeason(int $year, string $state) : void
    {
        $season = (new Season())->setYear($year)->setState($state);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($season);
        $entityManager->flush();
    }

    /**
     * Création de la saison courante
     * @return void
     */
    protected function createCurrentSeason() : void
    {
        $year = date('Y') - 2;
        $this->createSeason($year, 'CURRENT');
    }

    /**
     * Retourne la saison en fonction de l'année en paramètre
     * @param int $year
     * @return ?Season
     */
    protected function getSeasonByYear(int $year) : ?Season
    {
        $dql = strtr('SELECT seasons FROM :object seasons WHERE seasons.year = :year', [
            ':object' => Season::class,
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('year', $year)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne l'URI de la page de gestion de la saison
     * @return string
     */
    abstract protected function manageSeasonUri() : string;

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
     * Tentative de création d'une saison
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    protected function attemptManageSeason(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $this->manageSeasonUri());

        try {
            $submitButton = $crawler->filter('form[name=season] input[type=submit]');
            $formSeason = $submitButton->form();

            $formSeason['season[state]']->disableValidation();

            $postParams = [];
            foreach($params as $key => $value)
            {
                $fieldKey = 'season[' . $key . ']';
                $postParams[$fieldKey] = $value;
            }

            return $client->submit($formSeason, $postParams);
        } catch(\Throwable) {
            return $crawler;
        }
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
        $response = $this->attemptManageSeason($params);

        $expectedSeasonYear = $params['year'];

        // Vérification du message Flash
        $expectedFlashMessage = strtr($this->getSuccessMessageTemplate(), [
            ':year' => $expectedSeasonYear,
        ]);
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des saisons');
        $this->assertPageTitleSame('Liste des saisons');
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $managedSeason = $this->getSeasonByYear($expectedSeasonYear);
        $this->assertNotNull($managedSeason);

        // Vérification des données
        $expectedData = [
            'year' => $params['year'],
            'state' => $params['state'],
        ];
        $resultData = [
            'year' => $managedSeason?->getYear(),
            'state' => $managedSeason?->getState(),
        ];

        $this->assertEquals($expectedData, $resultData);

    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        return array(
            // Saison courante
            [
                [ 
                    'year' => (int) date('Y'),
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
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $this->createCurrentSeason();
        $this->createSeason(date('Y') - 1, 'ACTIVE');

       

        $latestSeason = $this->getLatestSeason();

        $response = $this->attemptManageSeason($params);

        $this->getSeasonByYear(date('Y'));

        $titleExpected = $this->getFailureExpectedPageTitle();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);

        // Vérifie qu'aucune saison n'a été ajouté
        $this->assertEquals($latestSeason, $this->getLatestSeason());

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
        return array(
            'empty' => [
                // Test paramètres vide
                [
                    'year' => '',
                    'state' => '',
                ], [
                    'year' => 'L\'année de la saison est nécessaire.',
                    'state' => 'Le statut de la saison est nécessaire.',
                ],
            ],
            'season_already_exists' => [
                // Test d'une saison existante
                [
                    'year' => date('Y') - 1,
                    'state' => 'ACTIVE',
                ], [
                    'year' => strtr('La saison :year existe déjà.', [ ':year' => date('Y') - 1 ]),
                ],
            ],
            'year_not_numeric' => [
                // Test lorsque l'année n'est pas un nombre
                [
                    'year' => 'pom',
                    'state' => 'DISABLED',
                ], [
                    'year' => 'L\'année doit être un nombre positif.',
                ],
            ],
            'year_error_regex' => [
                // Test lorsque l'année n'a pas quatre chiffres
                [
                    'year' => 25,
                    'state' => 'ACTIVE',
                ], [
                    'year' => 'L\'année doit être composée de quatre chiffres.',
                ],
            ],
            'incorrect_state' => [
                // Test lorsque l'état est incorrect
                [
                    'year' => date('Y') - 2,
                    'state' => 'ACTIVED',
                ], [
                    'state' => 'Le statut de la saison est incorrect.',
                ],
            ],
            'season_current_already_exists' => [
                // Test lorsque l'état CURRENT est déjà attribué
                [
                    'state' => 'CURRENT',
                ], [
                    'state' => strtr('La saison actuelle est attribuée à la saison :year.', [ ':year' => date('Y') - 2, ]),
                ]
            ],
        );
    }

    /*****************************************************************************/

}