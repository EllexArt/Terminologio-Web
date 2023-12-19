<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\UploadImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadImageController extends AbstractController
{
    #[Route('/upload/image', name: 'app_upload_image')]
    public function index(Request $request): Response
    {
        $concept = new Concept();
        $form = $this->createForm(UploadImageType::class, $concept);
        $form->handleRequest($request);
        return $this->render('upload_image/index.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }
}
