<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class UploadImageService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateFilename(string $originalName) : string
    {
        return date('dd-m-Y-H-i-s', null).$originalName.rand(0, 100);
    }

}