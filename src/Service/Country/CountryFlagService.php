<?php

/**
 * Service pour la gestion de l'image d'un pays
 */

namespace App\Service\Country;

use App\Entity\Country;

final class CountryFlagService {

    public const 
        VERSION_ORIGINAL = 'ORIGINAL',
        VERSION_SMALL = 'SMALL'
    ;
    private const DIRECTORY = 'images/countries/';

    /**
     * Constructeur
     * @param string $resourcesPath
     * @param string $resourcesUrl
     */
    public function __construct(private string $resourcesPath, private string $resourcesUrl)
    {

    }

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

        $relativeDirectory = self::DIRECTORY .  strtolower($version) . '/';
        $directory = $relativeDirectory;

        if($absolute)
        {
            $directory = $this->resourcesPath . $relativeDirectory;
        }

        return $directory;
    }

    /**
     * Retourne l'URL de l'image du pays
     * @param Country country
     * @return ?string
     */
    public function getImageUrl(Country $country) : ?string
    {
        $filename = $country->getImage();
        if($filename === null)
        {
            return null;
        }

        $filepath = $this->getVersionDirectory(self::VERSION_SMALL, absolute: false) . $filename;
        $url = $this->resourcesUrl . $filepath;
        return $url;
    }

}