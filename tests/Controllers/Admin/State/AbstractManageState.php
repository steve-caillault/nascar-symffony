<?php

/**
 * Tests des contrôleurs de gestion d'un pays iou d'un état
 */

namespace App\Tests\Controllers\Admin\State;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/***/
use App\Tests\Controllers\Admin\State\WithCountryCreation;
use App\Tests\WithUserCreating;
use App\Tests\BaseTestCase;
use App\Entity\AbstractStateEntity;

abstract class AbstractManageState extends BaseTestCase {

    use WithUserCreating, WithCountryCreation;

    /**
     * Retourne le titre de la page de redirection en cas de succès
     * @return string
     */
    abstract protected function getSuccessPageTitle() : string;

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
     * Retourne l'URI de la page de gestion de l'état
     * @return string
     */
    abstract protected function manageStateUri() : string;

    /**
     * Retourne le répertoire où sont stockées les images des drapeaux
     * @return string
     */
    abstract protected function getImagesDirectory() : string;

    /**
     * Retourne le nom du formulaire. Utilisez pour sélectionner le formulaire avec le Crawler.
     * @return string
     */
    abstract protected function getFormName() : string;

    /**
     * Nom de la classe de l'entité de l'état à utiliser
     * @return string
     */
    abstract protected function getStateEntityClass() : string;

    /**
     * Retourne l'état en fonction du code en paramètre
     * @param string $code
     * @return ?AbstractStateEntity
     */
    protected function getStateByCode(string $code) : ?AbstractStateEntity
    {
        $dql = strtr('SELECT states FROM :object states WHERE states.code = :code', [
            ':object' => $this->getStateEntityClass(),
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne le nombre d'états
     * @return int
     */
    protected function countStates() : int
    {
        $dql = strtr('SELECT COUNT(states.code) FROM :object states', [
            ':object' => $this->getStateEntityClass(),
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults(1)
            ->getSingleScalarResult()
        ;
    }

    /**
     * Tentative de création ou d'édition d'un état
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    protected function attemptManageState(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $this->manageStateUri());

        $formName = $this->getFormName();
        $submitSelector = 'form[name=' . $formName . '] input[type=submit]';

        try {

            $submitButton = $crawler->filter($submitSelector);
            $formState = $submitButton->form();

            $postParams = [];
            foreach($params as $key => $value)
            {
                $fieldKey = $formName . '[' . $key . ']';
                $postParams[$fieldKey] = $value;
            }

            return $client->submit($formState, $postParams);
        } catch(\Throwable) {
            return $crawler;
        }
    }

    /**
     * Vérification du succès de la gestion d'un état
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $response = $this->attemptManageState($params);

        // Vérification du message Flash
        $expectedFlashMessage = strtr($this->getSuccessMessageTemplate(), [
            ':name' => $params['name'],
        ]);
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $successPageTitle = $this->getSuccessPageTitle();
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', $successPageTitle);
        $this->assertPageTitleSame($successPageTitle);
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $managedState = $this->getStateByCode($params['code']);
        $this->assertNotNull($managedState);

        // Vérification des données
        $expectedData = [
            'code' => strtoupper($params['code']),
            'name' => $params['name'],
        ];
        $resultData = [
            'code' => $managedState?->getCode(),
            'name' => $managedState?->getName(),
        ];

        $this->assertEquals($expectedData, $resultData);

        $params['image'] ??= null;
        $withImage = ($managedState?->getImage() !== null or $params['image'] !== null);
        // Vérifie que les images existes
        if($withImage)
        {
            $this->assertNotNull($managedState->getImage());
            if($params['image']) // S'il s'agit d'une édition, on ne test pas car il faudrait générer le fichier
            {
                
                $resourcePath = $this->getService(ContainerBagInterface::class)->get('resources_path');
                $imagesDirectory = $resourcePath . $this->getImagesDirectory();
                $originalFilePath = $imagesDirectory . 'original/' .$managedState->getImage();
                $smallFilePath = $imagesDirectory . 'small/'. $managedState->getImage();
                $this->assertFileExists($originalFilePath);
                $this->assertFileExists($smallFilePath);
            }
        }
        // Vérifie que l'image n'a pas été affecté
        else
        {
            $this->assertNull($managedState->getImage());
        }
    }

    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        $faker = $this->getFaker();
        return array(
            // Pays sans image
            [
                [ 
                    'code' => $faker->countryCode(),
                    'name' => $faker->country(),
                ],
            ],
            // Pays avec image
            [
                [
                    'code' => $faker->countryCode(),
                    'name' => $faker->country(),
                    'image' => $faker->image(width: 200, height: 100),
                ]
            ],
        );
    }

    /**
     * Test de l'erreur d'un fichier trop volumineux pour le fichier de l'image
     * @return void
     */
    public function testFlagFileTooBig() : void
    {
        $imagesPath = $this->getService(ContainerBagInterface::class)->get('resources_path');
        $image = new UploadedFile($imagesPath . 'too-big.png', 'too-big.png', 'image/png', test: true);

        $params = [
            'code' => 'DE',
            'name' => $this->getFaker()->country(),
            'image' => $image,
        ]; 
        
        $errorsExpected = [
            'image' => 'L\'image du drapeau ne doit pas dépasser 1 MB.',
        ];

        $this->testValidationFailure($params, $errorsExpected);
    }

    /**
     * Test de l'erreur d'extension sur le fichier de l'image
     * @return void
     */
    public function testIncorrectFlagExtension() : void
    {
        $imagesPath = $this->getService(ContainerBagInterface::class)->get('resources_path');
        $image = new UploadedFile($imagesPath . 'not-an-image.sql', 'not-an-image.sql', 'plain/txt', test: true);
        
        $params = [
            'code' => 'GB',
            'name' => $this->getFaker()->country(),
            'image' => $image,
        ];
        
        $errorsExpected = [
            'image' => 'L\'image du drapeau doit être de type JPEG ou PNG.',
        ];

        $this->testValidationFailure($params, $errorsExpected);
    }

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @return void
     */
    public function testValidationFailure(array $params, array $errorsExpected) : void
    {
        $countStatesBeforeCalling = $this->countStates();

        $response = $this->attemptManageState($params);

        $titleExpected = $this->getFailureExpectedPageTitle();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);

        // Vérifie qu'aucun pays n'a été ajouté
        $this->assertEquals($countStatesBeforeCalling, $this->countStates());

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = $this->getFormName() . '[' . $field . ']';
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
                    'code' => '',
                    'name' => '',
                ], [
                    'code' => 'Le code ISO est nécessaire.',
                    'name' => 'Le nom est nécessaire.',
                ],
            ],
            'iso_code_not_letters' => [
                // Test si le code ISO est composé de lettres
                [
                    'code' => '12',
                    'name' => $faker->country(),
                ], [
                    'code' => 'Le code ISO ne doit contenir que des lettres.',
                ]
            ],
            'name_too_short' => [
                // Test si le nom est trop court
                [
                    'code' => 'US',
                    'name' => 'US',
                ], [
                    'name' => 'Le nom doit avoir au moins 3 caractères.',
                ]
            ],
            'name_too_long' => [
                // Test si le nom est trop long
                [
                    'code' => 'RU',
                    'name' => $faker->text(),
                ], [
                    'name' => 'Le nom ne doit pas avoir plus de 100 caractères.',
                ],
            ],
        );
    }
}