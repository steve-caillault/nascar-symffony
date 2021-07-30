<?php

/**
 * Tests du contrôleur de contact
 */

namespace App\Tests\Controllers\Site;

use DateTime;
use Symfony\Component\DomCrawler\Crawler;
/***/
use App\Tests\BaseTestCase;
use App\Entity\{
    ContactMessage,
    Log
};

final class ContactTest extends BaseTestCase {

    /**
     * Retourne le dernier message de contact
     * @return ?ContactMessage
     */
    private function getLatestContactMessage() : ?ContactMessage
    {
        $dql = strtr('SELECT message FROM :object message ORDER BY message.id DESC', [
            ':object' => ContactMessage::class,
        ]);
        return $this->getEntityManager()->createQuery($dql)->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * Tentative d'envoi d'un message de contact
     * @param array $param Paramètre à transmettre au formulaire
     * @return Crawler
     */
    private function attemptSendMessage(array $params) : Crawler
    {
        $client = $this->getHttpClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/contact');
      

        $submitButton = $crawler->filter('form#contact input[type=submit]');
        $formContact = $submitButton->form();

        $postParams = [];
        foreach($params as $key => $value)
        {
            $fieldKey = 'contact[' . $key . ']';
            $postParams[$fieldKey] = $value;
        }

        return $client->submit($formContact, $postParams);
    }

    /*****************************************************************************/

    /**
     * Vérification du succès de l'envoi d'un message
     * @param array Paramètres du message
     * @dataProvider successProvider
     * @return void
     */
    public function testSuccess(array $params) : void
    {
        $startingDate = (new \DateTimeImmutable(timezone: new \DateTimeZone('UTC')))->modify('-1 seconds');
        
        $response = $this->attemptSendMessage($params);

        $endingDate = (new \DateTimeImmutable(timezone: new \DateTimeZone('UTC')))->modify('+1 seconds');
        
        // Vérification du message Flash
        $expectedFlashMessage = 'Votre message a été envoyé avec succès.';
        $flashMessage = $response->filter('p.with-color.with-color-green')?->text();

        // Code et titres de la page
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Résultats NASCAR Cup Series');
        $this->assertPageTitleSame('Résultats, classement et statistiques du championnat NASCAR Cup Series');
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        $lastMessage = $this->getLatestContactMessage();
        $this->assertNotNull($lastMessage);

        // Vérification des données
        $expectedData = [
            'from' => $params['from'] ?? null,
            'email' => $params['email'] ?? null,
            'subject' => $params['subject'] ?? null,
            'message' => $params['message'],
        ];
        $resultData = [
            'from' => $lastMessage->getFrom(),
            'email' => $lastMessage->getEmail(),
            'subject' => $lastMessage->getSubject(),
            'message' => $lastMessage->getMessage()
        ];

        $this->assertEquals($expectedData, $resultData);

        // Vérification de la date
        $this->assertGreaterThanOrEqual($startingDate, $lastMessage->getCreatedAt());
        $this->assertLessThanOrEqual($endingDate, $lastMessage->getCreatedAt());

        // Vérification du nombre de messages en session
        $session = $this->getHttpClient()->getContainer()->get('session');
        $numberMessage = $session->get('ContactMessage::COUNT_SENDING');
        $this->assertEquals(1, $numberMessage);

        // Vérification du log en base de données
        $expectedMessageLog = strtr('Le message de contact :id a été envoyé.', [
            ':id' => $lastMessage->getId(),
        ]);
        $dqlLog = strtr('SELECT log FROM :object log WHERE log.message = :message ORDER BY log.id DESC', [
            ':object' => Log::class,
        ]);
        $log = $this->getEntityManager()->createQuery($dqlLog)->setParameter('message', $expectedMessageLog)->setMaxResults(1)->getResult();
        $this->assertNotNull($log);
    }
    
    /**
     * Provider pour les tests de succès
     * @return array
     */
    public function successProvider() : array
    {
        $faker = $this->getFaker();
        return array(
            // Test d'un message seul
            [
                [ 
                    'message' => $faker->realText(),
                ],
            ],
            // Test avec une adresse email
            [
                [ 
                    'message' => $faker->realText(),
                    'email' => $faker->email(),
                ],
            ],
            // Test avec un nom
            [
                [ 
                    'message' => $faker->realText(),
                    'from' => $faker->name(),
                ],
            ],
            // Test avec email et nom
            [
                [ 
                    'message' => $faker->realText(),
                    'from' => $faker->name(),
                    'email' => $faker->email(),
                ],
            ],
            // Test avec le sujet
            [
                [ 
                    'message' => $faker->realText(),
                    'subject' => $faker->realText(50),
                ],
            ],
            // Test avec tous les champs remplis
            [
                [ 
                    'message' => $faker->realText(),
                    'from' => $faker->name(),
                    'email' => $faker->email(),
                    'subject' => $faker->realText(50),
                ],
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Vérification des erreurs lors de l'envoi d'un message de contact
     * @param array $params Paramètres pour le formulaire
     * @dataProvider failureValidationProvider
     * @param array $errorsExpected 
     */
    public function testValidationFailure(array $params, array $errorsExpected)
    {
        $latestMessage = $this->getLatestContactMessage();
        $response = $this->attemptSendMessage($params);
        $newLatestMessage = $this->getLatestContactMessage();

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('h1', 'Envoyer un message');
        $this->assertPageTitleSame('Envoyer un message au gestionnaire du site Résultats NASCAR Cup Series');

        // Vérifie qu'aucun message n'a été ajouté
        $this->assertEquals($latestMessage, $newLatestMessage);

        // Vérification des messages d'erreurs
        foreach($errorsExpected as $field => $messageExpected)
        {
            $fieldKey = 'contact[' . $field . ']';
            $fieldSelector = '[name="' . $fieldKey . '"]';
            $error = $response->filter($fieldSelector)->closest('div.form-input')->filter('p.error')->text();
            $this->assertEquals($messageExpected, $error);
        }

        // Vérification du nombre de messages en session
        $session = $this->getHttpClient()->getContainer()->get('session');
        $numberMessage = $session->get('ContactMessage::COUNT_SENDING');
        $this->assertEquals(0, $numberMessage);
    }

    /**
     * Provider pour les tests d'échec de la validation
     * @return void
     */
    public function failureValidationProvider() : array
    {
        $faker = $this->getFaker();

        return array(
            [
                // Test sans message
                [], [
                    'message' => 'Le message ne doit pas être vide.',
                ],
            ],
            [
                // Message trop court
                [
                    'message' => 'pomme',
                ], [
                    'message' => 'Le message doit avoir au moins 10 caractères.',
                ],
            ],
            [
                // Test de message trop long
                [
                    'message' => $faker->realText(70000),
                ], [
                    'message' => 'Le message ne doit pas avoir plus de 65535 caractères.'
                ],
            ],
            [
                // Test de sujet trop court
                [
                    'subject' => 'pois',
                    'message' => $faker->realText(),
                ], [
                    'subject' => 'Le sujet du message doit avoir au moins 5 caractères.',
                ],
            ],
            [
                // Test de sujet trop long
                [
                    'subject' => $faker->realText(),
                    'message' => $faker->realText(),
                ], [
                    'subject' => 'Le sujet du message ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            [
                // Test d'adresse email trop longue
                [
                    'email' => $faker->realText(),
                ], [
                    'email' => 'L\'adresse email ne doit pas avoir plus de 100 caractères.',
                ],
            ],
            [
                // Test d'adresse email incorrecte
                [
                    'email' => $faker->name(),
                ], [
                    'email' => 'Ce n\'est pas une adresse mail valide.',
                ],
            ],
            [
                // Test de nom trop court
                [
                    'from' => 'pom',
                ], [
                    'from' => 'Le nom doit avoir au moins 5 caractères.',
                ],
            ],
            [
                // Test de nom trop long
                [
                    'from' => $faker->realText(),
                ], [
                    'from' => 'Le nom ne doit pas avoir plus de 100 caractères.',
                ],
            ],
        );
    }

    /*****************************************************************************/

    /**
     * Test après 5 messages dans la session
     * @return void
     */
    public function testTooMuchMessages() : void
    {
        $latestMessage = $this->getLatestContactMessage();

        $client = $this->getHttpClient();

        $session = $client->getContainer()->get('session');
        $session->set('ContactMessage::COUNT_SENDING', 5);
        $session->save();

        $client->followRedirects();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseStatusCodeSame(200);

        // Vérification du message Flash
        $expectedFlashMessage = 'Vous avez envoyé trop de message.';
        $flashMessage = $crawler->filter('p.with-color.with-color-red')?->text();
        $this->assertEquals($expectedFlashMessage, $flashMessage);

        // Vérifie qu'il n'y a pas eu de nouveau message enregistré
        $newLatestMessage = $this->getLatestContactMessage();
        $this->assertEquals($latestMessage, $newLatestMessage);

        // Vérifie que le formulaire de contact n'est pas présent
        $form = $crawler->filter('form#contact');
        $this->assertEquals(0, $form->count());
    }

    /*****************************************************************************/

}