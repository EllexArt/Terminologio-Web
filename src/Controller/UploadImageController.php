<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\UploadImageType;
use App\Service\UploadImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadImageController extends AbstractController
{
    private static string $directory = '/data/images';
    #[Route('/upload/image', name: 'app_upload_image')]
    public function index(Request $request, UploadImageService $uploadImageService): Response
    {
        $concept = new Concept();
        $form = $this->createForm(UploadImageType::class, $concept);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['attachment']->getData();
            $newFilename = $uploadImageService->generateFilename($file);
            $file->move(UploadImageController::$directory, $newFilename);

        // ...
        }
        return $this->render('upload_image/index.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }
}
