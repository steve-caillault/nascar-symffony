<?php

/**
 * Service pour la gestion de l'image d'un pays ou d'un état
 */

namespace App\Service\State;

use App\Entity\AbstractStateEntity as State;

abstract class AbstractFlagService {

    public const 
        VERSION_ORIGINAL = 'ORIGINAL',
        VERSION_SMALL = 'SMALL'
    ;

    /**
     * Constructeur
     * @param string $resourcesPath
     * @param string $resourcesUrl
     */
    public function __construct(private string $resourcesPath, private string $resourcesUrl)
    {

    }

    /**
     * Retourne le répertoire où sont stockées les images
     * @return string
     */
    abstract protected function getDirectory() : string;

    /**
     * Retourne le répertoire de la version où sont stockées les images
     * @param string $version
     * @param bool $absolute Vrai s'il faut retourner le chemin absolu
     * @return string
     */
    public function getVersionDirectory(string $version, bool $absolute = true) : string
    {
        $allowedVersions = [ self::VERSION_ORIGINAL, self::VERSION_SMALL ];
        if(! in_array($version, $allowedVersions))
        {
            throw new \Exception('Incorrect image version.', 500);
        }

        $relativeDirectory = $this->getDirectory() .  strtolower($version) . '/';
        $directory = $relativeDirectory;

        if($absolute)
        {
            $directory = $this->resourcesPath . $relativeDirectory;
        }

        return $directory;
    }

    /**
     * Retourne l'URL de l'image du drapeau
     * @param State $state
     * @return ?string
     */
    public function getImageUrl(State $state) : ?string
    {
        $filename = $state->getImage();
        if($filename === null)
        {
            return null;
        }

        $filepath = $this->getVersionDirectory(self::VERSION_SMALL, absolute: false) . $filename;
        $url = $this->resourcesUrl . $filepath;
        return $url;
    }

}