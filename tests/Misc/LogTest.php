<?php

/**
 * Tests d'enregistrement de log
 */

namespace App\Tests\Misc;

use Symfony\Component\Routing\RouterInterface;
/***/
use App\Tests\BaseTestCase;
use App\Entity\Log;

final class LogTest extends BaseTestCase {

    /**
     * Vérification de l'enregistrement
     * @return void
     */
    public function testSaving() : void
    {
        $faker = $this->getFaker();
        $message = $faker->text();
        $userAgent = $faker->userAgent();

        $uri = $this->getService(RouterInterface::class)->generate('app_testing_default_log', [
            'message' => $message,
        ]);

        $client = $this->getHttpClient();
        $client->setServerParameters([
            'HTTP_USER_AGENT' => $userAgent,
        ]);
        $client->request('GET', $uri);

        $repository = $this->getRepository(Log::class);
        $lastLog = $repository->findOneBy([], orderBy: [ 'date' => 'desc' ]);

        $dataExpected = [
            'uri' => $uri,
            'message' => $message,
            'user_agent' => $userAgent,
            'level' => 'DEBUG',
        ];

        $dataCurrent = [
            'level' => $lastLog?->getLevel(),
            'uri' => $lastLog?->getUri(),
            'message' => $lastLog?->getMessage(),
            'user_agent' => $lastLog?->getUserAgent(),
        ];

        $this->assertEquals($dataExpected, $dataCurrent);
    }

}