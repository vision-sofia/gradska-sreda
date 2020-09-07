<?php

namespace App\Services;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;

class UploaderHelper
{
    private FilesystemInterface $filesystem;

    public function __construct(FilesystemInterface $publicUploadsFilesystem)
    {
        $this->filesystem = $publicUploadsFilesystem;
    }

    public function uploadAnswerImage(File $file): ?string
    {
        $newFilename = bin2hex(random_bytes(12)) . '.' . $file->guessExtension();

        $contents = file_get_contents($file->getPathname());

        if (!$contents) {
            return null;
        }

        $this->filesystem->write($newFilename, $contents);

        return $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        //return $this->requestStackContext->getBasePath() . '/uploads/' . $path;
        return '';
    }
}
