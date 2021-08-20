<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
/***/
use App\Entity\Country;

final class UploadCountryFlagService {

    private const DIRECTORY = 'images/countries/';

    /**
     * Répertoire où sont stockées les images
     * @var string
     */
    private string $directory;

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
        private string $resourcesPath, 
        private OldUploadedFilesService $oldUploadedFilesService
    )
    {
        $this->directory = $resourcesPath . self::DIRECTORY;
        $this->fileSystem = new Filesystem();
    }

    

    /**
     * Cré la version original
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
        $directoryDest = $this->directory . 'original/';

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

        $directory = $this->directory . 'small/';

        // Ancien fichier à supprimer
        $previousFile = $country->getImage();
        $previousFilePath = ($previousFile !== null) ? ($directory . $previousFile) : null;
        if($previousFilePath !== null)
        {
            $this->oldUploadedFilesService->addFileToDelete($previousFilePath);
        }
        
        // Cré le répertoire s'il n'existe pas
        $this->fileSystem->mkdir($directory);

        // Enregistrement du fichier
        $destinationFile = $directory . $imageInterventionVersion->basename;

        try {
            $imageInterventionVersion->save($destinationFile);
        } catch (\Exception) {
            return false;
        }
        
        return $this->fileSystem->exists($destinationFile);
    }

}