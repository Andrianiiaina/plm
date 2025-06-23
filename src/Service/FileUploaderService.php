<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, String $folder): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

       
        try {
             $targetDir = $this->getTargetDirectory() . '/' . $folder;
            // Ensure directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $file->move($this->getTargetDirectory()."/".$folder, $fileName);
        } catch (FileException $e) {
            error_log('File upload failed: ' . $e->getMessage());
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
    
    public function removeFileFromStorage($folder,$file){
        $path=$this->getTargetDirectory()."/".$folder."/".$file;
        if (file_exists($path)) {
            unlink($path);
        }
    }



}