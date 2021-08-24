<?php

namespace App\Service\State;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
/***/
use App\Service\OldUploadedFilesService;
use App\Entity\AbstractStateEntity as State;

final class UploadStateFlagService {

    /**
     * Gestionnaire de fichier
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * Constructeur
     * @param string $resourcesPath
     */
    public function __construct(
        private FlagServiceFactory $flagServiceFactory,
        private OldUploadedFilesService $oldUploadedFilesService
    )
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * Retourne le service de gestion du drapeau en fonction de l'état
     * @param State $state
     * @return AbstractFlagService
     */
    private function getFlagService(State $state) : AbstractFlagService
    {
        return $this->flagServiceFactory->get($state);
    }

    /**
     * Tentative de création de l'image et de ses versions
     * Si la tentative réussie, la méthode retourne le nom du fichier de l'image originale
     * @param UploadedFile $uploadedFile
     * @param State $country
     * @return ?string Fichier de l'image créée
     */
    public function attempt(UploadedFile $uploadedFile, State $state) : ?string
    {
        $realpath = $uploadedFile->getRealPath();

        if($realpath === null)
        {
            return null;
        }

        $extension = strtolower($uploadedFile->guessExtension());
        $fileName = hash('crc32', rand() . time() . rand()) . '.' . $extension;
        $directoryDest = $this->getFlagService($state)->getVersionDirectory(AbstractFlagService::VERSION_ORIGINAL);

        try {
            $uploadedFile->move($directoryDest, $fileName);
        } catch (\Exception) {
            return null;
        }

        $expectedFullPath = $directoryDest . $fileName;
        $originalFullPath = ($this->fileSystem->exists($expectedFullPath)) ? $expectedFullPath : null;

        // Création des versions
        if($originalFullPath === null or ! $this->createVersions($originalFullPath, $state))
        {
            return null;
        }

        // Détermine l'ancienne version du fichier à supprimer
        $previousFile = $state->getImage();
        $previousFilePath = ($previousFile !== null) ? ($directoryDest . $previousFile) : null;
        if($previousFilePath !== null)
        {
            $this->oldUploadedFilesService->addFileToDelete($previousFilePath);
        }

        return $fileName;
    }

    /**
     * Création des diffèrentes versions de l'image
     * @param string $originalPath Chemin d'accès vers l'image $originale
     * @param State $state
     * @return bool Vrai si toutes les versions ont été créées
     */
    private function createVersions(string $originalPath, State $state) : bool
    {
        $imageManager = new ImageManager;
        $imageInterventionVersion = $imageManager->make($originalPath);

        $imageInterventionVersion->resize(100, 50, function($constraint) {
            $constraint->aspectRatio();
        });

        $directoryDest = $this->getFlagService($state)->getVersionDirectory(AbstractFlagService::VERSION_SMALL);

        // Ancien fichier à supprimer
        $previousFile = $state->getImage();
        $previousFilePath = ($previousFile !== null) ? ($directoryDest . $previousFile) : null;
        if($previousFilePath !== null)
        {
            $this->oldUploadedFilesService->addFileToDelete($previousFilePath);
        }
        
        // Cré le répertoire s'il n'existe pas
        $this->fileSystem->mkdir($directoryDest);

        // Enregistrement du fichier
        $destinationFile = $directoryDest . $imageInterventionVersion->basename;

        try {
            $imageInterventionVersion->save($destinationFile);
        } catch (\Exception) {
            return false;
        }
        
        return $this->fileSystem->exists($destinationFile);
    }

}