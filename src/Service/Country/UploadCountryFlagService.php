<?php

namespace App\Service\Country;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
/***/
use App\Service\OldUploadedFilesService;
use App\Entity\Country;

final class UploadCountryFlagService {

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
        private CountryFlagService $countryFlagService,
        private OldUploadedFilesService $oldUploadedFilesService
    )
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * Tentative de création de l'image et de ses versions
     * Si la tentative réussie, la méthode retourne le nom du fichier de l'image originale
     * @param UploadedFile $uploadedFile
     * @param Country $country
     * @return ?string Fichier de l'image créée
     */
    public function attempt(UploadedFile $uploadedFile, Country $country) : ?string
    {
        $realpath = $uploadedFile->getRealPath();

        if($realpath === null)
        {
            return null;
        }

        $extension = strtolower($uploadedFile->guessExtension());
        $fileName = hash('crc32', rand() . time() . rand()) . '.' . $extension;
        $directoryDest = $this->countryFlagService->getVersionDirectory(CountryFlagService::VERSION_ORIGINAL);

        try {
            $uploadedFile->move($directoryDest, $fileName);
        } catch (\Exception $e) {
            return null;
        }

        $expectedFullPath = $directoryDest . $fileName;
        $originalFullPath = ($this->fileSystem->exists($expectedFullPath)) ? $expectedFullPath : null;

        // Création des versions
        if($originalFullPath === null or ! $this->createVersions($originalFullPath, $country))
        {
            return null;
        }

        // Détermine l'ancienne version du fichier à supprimer
        $previousFile = $country->getImage();
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
     * @param Country $country
     * @return bool Vrai si toutes les versions ont été créées
     */
    private function createVersions(string $originalPath, Country $country) : bool
    {
        $imageManager = new ImageManager;
        $imageInterventionVersion = $imageManager->make($originalPath);

        $imageInterventionVersion->resize(100, 50, function($constraint) {
            $constraint->aspectRatio();
        });

        $directoryDest = $this->countryFlagService->getVersionDirectory(CountryFlagService::VERSION_SMALL);

        // Ancien fichier à supprimer
        $previousFile = $country->getImage();
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