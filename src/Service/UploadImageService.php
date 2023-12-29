<?php

namespace App\Service;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadImageService
{

    public function uploadImage(SluggerInterface $slugger, mixed $image, string $directory) : ?string
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

        try {
            $image->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
            return null;
        }
        return $newFilename;
    }

    public function deleteImage(string $directory, string $filename): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($directory.'/'.$filename);
    }
}