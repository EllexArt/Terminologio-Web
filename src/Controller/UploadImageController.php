<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\UploadImageType;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadImageController extends AbstractController
{
    #[Route('/upload/image', name: 'app_upload_image')]
    public function index(Request $request, EntityManagerInterface $entityManager, UploadImageService $uploadImageService, SluggerInterface $slugger): Response
    {
        $concept = new Concept();
        $form = $this->createForm(UploadImageType::class, $concept);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );
            } catch (FileException $e) {

            }

            $concept->setImage($newFilename);
            $entityManager->persist($concept);
            $entityManager->flush();
            return $this->redirectToRoute('app_terminologio_index');
        }
        return $this->render('upload_image/index.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }
}
