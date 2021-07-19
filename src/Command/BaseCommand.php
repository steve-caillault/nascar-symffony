<?php

/**
 * Classe abstraite pour les commandes
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

abstract class BaseCommand extends Command
{
    /**
     * Logger
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Modifie le logger
     * @param LoggerInterface $logger
     * @return void
     * @required
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * Retourne le statut en loguant le message
     * @param OutputInterface $output
     * @param string $message
     * @param int $status
     * @return int
     */
    protected function exitWithMessage(OutputInterface $output, string $message, int $status) : int
    {
        $logMethod = ($status === 0) ? 'info' : 'debug';

        $this->logger->{ $logMethod }($message);
        $output->write($message . PHP_EOL);

        return $status;
    }

}