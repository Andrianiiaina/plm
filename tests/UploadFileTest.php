<?php

namespace App\Tests;

use App\Service\FileUploaderService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class UploadFileTest extends TestCase
{
    private string $targetDirectory = __DIR__ . '/uploads_test';

    public function testGetTargetDirectory(): void
    {
        $sluggerMock = $this->createMock(SluggerInterface::class);
        $fileUploader = new FileUploaderService($this->targetDirectory, $sluggerMock);

        $this->assertEquals($this->targetDirectory, $fileUploader->getTargetDirectory());
    }

    public function testUpload(): void
    {
        // On crée un faux fichier
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, 'dummy content');
        $file = new UploadedFile($filePath, 'testfile.txt', null, null, true);

        // Mock du slugger pour éviter une vraie transformation du nom
        $sluggerMock = $this->createMock(SluggerInterface::class);
        $sluggerMock->method('slug')->willReturn(new UnicodeString('testfile'));
        $fileUploader = new FileUploaderService($this->targetDirectory, $sluggerMock);
        $folder = 'testFolder';

        // Exécute l'upload
        $newFileName = $fileUploader->upload($file, $folder);

        // Vérifie si le fichier a bien été déplacé
        $expectedPath = $this->targetDirectory . '/' . $folder . '/' . $newFileName;
        $this->assertFileExists($expectedPath);

        // Nettoyage après le test
        unlink($expectedPath);
        rmdir($this->targetDirectory . '/' . $folder);
    }

    public function testRemoveFileFromStorage(): void
    {
        $sluggerMock = $this->createMock(SluggerInterface::class);
        $fileUploader = new FileUploaderService($this->targetDirectory, $sluggerMock);

        $folder = 'testFolder';
        $fileName = 'testfile.txt';
        $filePath = $this->targetDirectory . '/' . $folder . '/' . $fileName;

        // Création d'un faux fichier
        mkdir($this->targetDirectory . '/' . $folder, 0777, true);
        file_put_contents($filePath, 'dummy content');

        // Vérifie que le fichier existe bien avant suppression
        $this->assertFileExists($filePath);

        // Supprime le fichier
        $fileUploader->removeFileFromStorage($folder, $fileName);

        // Vérifie que le fichier a été supprimé
        $this->assertFileDoesNotExist($filePath);

        // Nettoyage
        rmdir($this->targetDirectory . '/' . $folder);
    }
}
