<?php

/**
 * Mise en maintenance et 
 * php bin/console maintenance true|false
 */

namespace App\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\{
    Validation,
    Constraints
};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{ 
    InputInterface, 
    InputArgument 
};
use Symfony\Component\Console\Output\OutputInterface;

final class MaintenanceCommand extends BaseCommand
{
    /**
     * @inheritdoc
     */
    protected static $defaultName = 'maintenance';

    /********************************************************/

    /**
     * Constructeur
     * @param string $maintenanceFilePath Chemin d'accès au fichier de la maintenance
     */
    public function __construct(private string $maintenanceFilePath)
    {
        parent::__construct(static::$defaultName);
    }

    /********************************************************/

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setDescription('Mise en maintenance et réactivation du site.')
            ->addArgument('activeMaintenance', InputArgument::REQUIRED, 'Vrai s\'il faut activer la maintenance, faux sinon.')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $activeMaintenance = $input->getArgument('activeMaintenance');
    
        $data = [
            'active' => $activeMaintenance,
        ];

        $constraints = new Constraints\Collection([
            'active' => [
                new Constraints\NotBlank(message: 'Le paramètre activeMaintenance ne doit pas être vide.'),
                new Constraints\Regex('/^(true|false)$/D', message: 'Le paramètre activeMaintenance doit être true ou false.')
            ],
        ]);

        $errors = Validation::createValidator()->validate($data, $constraints);
        if($errors->count() > 0)
        {
            $messages = [];
            foreach($errors as $error)
            {
                $messages[] = $error->getMessage();
            }
            $message = implode(' ', $messages);
            return $this->exitWithMessage($output, $message, Command::FAILURE);
        }

        return match($activeMaintenance) {
            'true' => $this->enableMaintenance($output),
            'false' => $this->disableMaintenance($output),
            default => throw new \Exception('Paramètre incorrect.')
        };
    }

    /**
     * Active la maintenance
     * @param OutputInterface $output
     * @return int
     */
    private function enableMaintenance(OutputInterface $output) : int
    {
        $filePath = $this->maintenanceFilePath;
        $alreadyEnabled = file_exists($filePath);

        if($alreadyEnabled)
        {
           $message = 'La maintenance est déjà activée.';
           $responseStatus = self::SUCCESS;
        }
        else
        {
            
            (new Filesystem())->dumpFile($filePath, '');
            $responseStatus = (file_exists($filePath)) ? self::SUCCESS : self::FAILURE;
            $message = ($responseStatus === self::SUCCESS) ? 'La maintenance a été activée.' : 'La maintenance n\'a pas pu être activée.';
        }

        return $this->exitWithMessage($output, $message, $responseStatus);
    }

    /**
     * Désactive la maintenance
     * @param OutputInterface $output
     * @return int
     */
    private function disableMaintenance(OutputInterface $output) : int
    {
        $filePath = $this->maintenanceFilePath;
        $alreadyDisabled = (! file_exists($filePath));

        if($alreadyDisabled)
        {
           $message = 'La maintenance est déjà désactivée.';
           $responseStatus = self::SUCCESS;
        }
        else
        {
            unlink($filePath);
            $responseStatus = (! file_exists($filePath)) ? self::SUCCESS : self::FAILURE;
            $message = ($responseStatus === self::SUCCESS) ? 'La maintenance a été désactivée.' : 'La maintenance n\'a pas pu être désactivée.';
        }

        return $this->exitWithMessage($output, $message, $responseStatus);
    }

}