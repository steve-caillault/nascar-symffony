<?php

/**
 * Service gérant les fichiers uploadés à supprimer
 */

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

final class OldUploadedFilesService {
    
    /**
     * Stocke les fichiers à supprimer
     * @var array
     */
    private array $filesToDelete = [];

    /**
     * Ajout le chemin du fichier en paramètre à ceux qu'il faut supprimer
     * @param string $path
     * @return self
     */
    public function addFileToDelete(string $path) : self
    {
        $canAdd = (! in_array($path, $this->filesToDelete));
        if($canAdd)
        {
            $this->filesToDelete[] = $path;
        }
        return $this;
    }

    /**
     * Suppression des fichiers
     * @return void
     */
    public function delete() : void
    {
        $fileSystem = new Filesystem();
        $filesToDelete = $this->filesToDelete;
        try {
            foreach($filesToDelete as $path)
            {
                $fileSystem->remove($path);
            }
        } catch(\Exception) {
            
        }
    }

}