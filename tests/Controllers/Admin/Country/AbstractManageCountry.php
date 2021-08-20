<?php

/**
 * Tests des contrôleurs de gestion d'un pays
 */

namespace App\Tests\Controllers\Admin\Country;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/***/
use App\Tests\WithUserCreating;
use App\Tests\BaseTestCase;
use App\Entity\Country;

abstract class AbstractManageCountry extends BaseTestCase {

    use WithUserCreating;

    /**
     * Retourne le nombre de pays
     * @return int
     */
    private function countCountries() : int
    {
        $dql = strtr('SELECT COUNT(countries.code) FROM :object countries', [
            ':object' => Country::class,
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults(1)
            ->getSingleScalarResult()
        ;
    }

    /**
     * Création d'une pays
     * @param string $code
     * @param string $name
     * @param ?string $image
     * @return void
     */
    protected function createCountry(string $code, string $name, ?string $image = null) : void
    {
        $country = (new Country())->setCode($code)->setName($name)->setImage($image);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($country);
        $entityManager->flush();
    }

    /**
     * Retourne le pays en fonction du code en paramètre
     * @param string $code
     * @return ?Country
     */
    protected function getCountryByCode(string $code) : ?Country
    {
        $dql = strtr('SELECT countries FROM :object countries WHERE countries.code = :code', [
            ':object' => Country::class,
        ]);

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    /**
     * Retourne l'URI de la page de gestion du pays
     * @return string
     */
    abstract protected function manageCountryUri() : string;

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
     * Tentative de création d'un pays
     * @param array $params Paramètres du formulaire
     * @return Crawler
     */
    protected function attemptManageCountry(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->loginUser($this->userToLogged(), 'admin');
        $client->followRedirects();
        $crawler = $client->request('GET', $this->manageCountryUri());

        try {
            $submitButton = $crawler->filter('form[name=country] input[type=submit]');
            $formCountry = $submitButton->form();

            $postParams = [];
            foreach($params as $key => $value)
            {
                $fieldKey = 'country[' . $key . ']';
                $postParams[$fieldKey] = $value;
            }

            return $client->submit($formCountry, $postParams);
        } catch(\Throwable) {
            return $crawler;
        }
    }

    /*****************************************************************************/

    /**
     * Vérification du succès de la création d'un pays
     * @param array Paramètres du formulaire
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $countCountriesBeforeCalling = $this->countCountries();

        $response = $this->attemptManageCountry($params);

        // Vérification du message Flash
        $expectedFlashMessage = strtr($this->getSuccessMessageTemplate(), [
            ':name' => $params['name'],
        ]);
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Liste des pays');
        $this->assertPageTitleSame('Liste des pays');
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $managedCountry = $this->getCountryByCode($params['code']);
        $this->assertNotNull($managedCountry);

        // Vérification des données
        $expectedData = [
            'code' => strtoupper($params['code']),
            'name' => $params['name'],
        ];
        $resultData = [
            'code' => $managedCountry?->getCode(),
            'name' => $managedCountry?->getName(),
        ];

        $this->assertEquals($expectedData, $resultData);

        $params['image'] ??= null;
        // Vérifie que les images existes
        if($params['image'] !== null)
        {
            $this->assertNotNull($managedCountry->getImage());
            $resourcePath = $this->getService(ContainerBagInterface::class)->get('resources_path');
            $originalFilePath = $resourcePath . 'images/countries/original/' . $managedCountry->getImage();
            $smallFilePath = $resourcePath . 'images/countries/small/' . $managedCountry->getImage();
            $this->assertFileExists($originalFilePath);
            $this->assertFileExists($smallFilePath);
        }
        // Vérifie que l'image n'a pas été affecté
        else
        {
            $this->assertNull($managedCountry->getImage());
        }

        // Vérifie que le nombre de pays a augmenté
        $this->assertEquals($countCountriesBeforeCalling + 1, $this->countCountries());
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

    /*****************************************************************************/

    /**
     * Vérification des erreurs lors de la création d'une saison
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     * @param bool $imageExtensionError Vrai s'il faut générer un fichier qui générera une erreur sur l'extension
     * @param bool $imageSizeToBigError Vrai s'il faut générer un fichier qui générera une erreur sur la taille du fichier
     * @return void
     */
    public function testValidationFailure(
        array $params, 
        array $errorsExpected, 
        bool $imageExtensionError = false,
        bool $imageSizeToBigError = false
    ) : void
    {

        $imagesPath = $this->getService(ContainerBagInterface::class)->get('resources_path');

        // On est obligé d'affecter ici le fichier car dataProvider est appelé avant les tests
        if($imageExtensionError)
        {
            $params['image'] = new UploadedFile($imagesPath . 'not-an-image.sql', 'not-an-image.sql', 'plain/txt', test: true);
        }
        elseif($imageSizeToBigError)
        {
            $params['image'] = new UploadedFile($imagesPath . 'too-big.png', 'too-big.png', 'image/png', test: true);
        }

        $this->createCountry('FR', 'France');

        $countCountriesBeforeCalling = $this->countCountries();

        $response = $this->attemptManageCountry($params);

        $titleExpected = $this->getFailureExpectedPageTitle();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', $titleExpected);
        $this->assertPageTitleSame($titleExpected);

        // Vérifie qu'aucun pays n'a été ajouté
        $this->assertEquals($countCountriesBeforeCalling, $this->countCountries());

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = 'country[' . $field . ']';
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
                    'code' => 'Le code ISO du pays est nécessaire.',
                    'name' => 'Le nom du pays est nécessaire.',
                ],
            ],
            'country_already_exists' => [
                // Test d'un pays existant
                [
                    'code' => 'fr',
                    'name' => $faker->country(),
                ], [
                    'code' => 'Le pays "FR" existe déjà.',
                ],
            ],
            'iso_code_too_long' => [
                // Test si le code ISO à 2 caractères
                [
                    'code' => 'frr',
                    'name' => $faker->country(),
                ], [
                    'code' => 'Le code ISO du pays doit être formé de deux lettres.',
                ],
            ],
            'iso_code_not_letters' => [
                // Test si le code ISO est composé de lettres
                [
                    'code' => '12',
                    'name' => $faker->country(),
                ], [
                    'code' => 'Le code ISO doit être composé de deux lettres.',
                ]
            ],
            'name_too_short' => [
                // Test si le nom du pays est trop court
                [
                    'code' => 'US',
                    'name' => 'US',
                ], [
                    'name' => 'Le nom du pays doit avoir au moins 3 caractères.',
                ]
            ],
            'name_too_long' => [
                // Test si le nom du pays est trop long
                [
                    'code' => 'RU',
                    'name' => $faker->text(),
                ], [
                    'name' => 'Le nom du pays ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            'image_extension_incorrect' => [
                // Test si l'extension de l'image est correcte
                [
                    'code' => 'GB',
                    'name' => $faker->country(),
                ], [
                    'image' => 'L\'image du drapeau doit être de type JPEG ou PNG.',
                ],
                true
            ],
            'image_size_too_big' => [
                // Test si le poids de l'image est trop grand
                [
                    'code' => 'DE',
                    'name' => $faker->country(),
                ], [
                    'image' => 'L\'image du drapeau ne doit pas dépasser 1 MB.',
                ],
                false,
                true
            ],
        );
    }

    /*****************************************************************************/

}