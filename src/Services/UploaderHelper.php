<?php

namespace App\Services;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;

class UploaderHelper
{
    private $filesystem;

    public function __construct(FilesystemInterface $publicUploadsFilesystem)
    {
        $this->filesystem = $publicUploadsFilesystem;
    }

    public function uploadAnswerImage(File $file): string
    {
        $newFilename = bin2hex(random_bytes(12)) . '.' . $file->guessExtension();

        $this->filesystem->write(
            $newFilename,
            file_get_contents($file->getPathname())
        );

        return $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        //return $this->requestStackContext->getBasePath() . '/uploads/' . $path;
        return '';
    }
}
