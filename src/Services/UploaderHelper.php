<?php


namespace App\Services;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $mediaDir;

    public function __construct(string $mediaDir)
    {

        $this->mediaDir = $mediaDir;
    }

    public function uploadDataFile(UploadedFile $uploadedFile): File
    {
        $newFilename = bin2hex(random_bytes(12)) . '.' . $uploadedFile->guessExtension();

        $file = $uploadedFile->move(
            $this->mediaDir,
            $newFilename
        );

        return $file;
    }
}
