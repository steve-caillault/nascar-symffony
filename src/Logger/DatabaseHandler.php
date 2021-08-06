<?php

/**
 * Gestion d'un enregistrement de log en base de données
 * see https://symfony.com/doc/current/logging/handlers.html
 */

namespace App\Logger;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{ 
    Request, 
    RequestStack 
};
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LogLevel;
/***/
use App\Entity\Log;

final class DatabaseHandler extends AbstractProcessingHandler
{
    /**
     * Nom du site
     * @var string
     */
    private string $siteName;

    /**
     * Pile de requêtes
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * Gestionnaire d'entité
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Affecte le nom du site
     * @param string $siteName
     * @return void
     * @required
     */
    public function setSIteName(string $siteName) : void
    {
        $this->siteName = $siteName;
    }

    /**
     * Modifie la pile de requêtes HTTP
     * @param RequestStack $requestStack
     * @return void
     * @required
     */
    public function setRequestStack(RequestStack $requestStack) : void
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Modifie le gestionnaire d'entité
     * @param EntityManagerInterface $entityManager
     * @return void
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager) : void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Enregistre le log
     * @param array $record
     * @return void
     */
    protected function write(array $record) : void
    {
        $request = $this->requestStack->getCurrentRequest();
        
        $timezone = new \DateTimeZone('UTC');
        $datetime = $record['datetime']?->setTimezone($timezone) ?? new \DateTime(timezone: $timezone);

        $userAgent = $request?->headers->get('User-Agent');

        $uri = $request?->getRequestUri();
        $message = $record['message'] ?? '';
        $level = $record['level_name'] ?? LogLevel::ERROR;
        $siteName = $this->siteName;

        $log = (new Log())
            ->setSiteName($siteName)
            ->setDate($datetime)
            ->setUri($uri)
            ->setLevel($level)
            ->setMessage($message)
            ->setUserAgent($userAgent)
        ;

        try {
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        } catch(\Exception) {
            
        }

    }
}