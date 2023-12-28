<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\ConceptUploadType;
use App\Repository\ComponentNameRepository;
use App\Repository\ConceptRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptController extends AbstractController
{
    #[Route('/concept/create', name: 'app_concept_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager, ConceptService $conceptService, SluggerInterface $slugger, ConceptRepository $conceptRepository): Response
    {
        $concept = new Concept();
        $form = $this->createForm(ConceptUploadType::class, $concept);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($conceptRepository->findOneBy(['title' => $concept->getTitle()]) != null) {
                $this->addFlash('warning', 'Title already taken');
                return $this->render('concept/create_concept.html.twig', [
                    'uploadForm' => $form->createView(),
                ]);
            }

            $image = $form->get('image')->getData();
            $user = $this->getUser();

            $conceptService->uploadConcept($image, $user, $slugger, $concept, $entityManager, $this->getParameter('image_directory'));

            return $this->redirectToRoute('app_concept_component_edit', [
                'title' => $concept->getTitle()]);
        }
        return $this->render('concept/create_concept.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }

    #[Route('/concept/{title}/component', name: 'app_concept_component_edit')]
    public function addComponentsToConcept(ConceptService $conceptService, Concept $concept): Response
    {
        if($concept->isIsValidated()) {
            return $this->redirectToRoute('app_concept_show', [
                'title' => $concept->getTitle(),
            ]);
        }
        return $this->render('concept/edit_concept.html.twig', [
            'components' => $conceptService->calculateComponentsWithDefaultTrad($concept),
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/validate', name: 'app_concept_validate', methods: 'POST')]
    public function validateConcept(ConceptService $conceptService, ComponentNameRepository $componentNameRepository, EntityManagerInterface $entityManager, Concept $concept, Request $request): Response
    {
        if(sizeof($concept->getComponents()) == 0) {
            $this->addFlash('warning', 'You need to specify at least one component');
            return $this->redirectToRoute('app_concept_component_edit', [
                'title' => $concept->getTitle(),
            ]);
        }
        $conceptService->saveComponentNames($concept, $request, $componentNameRepository, $concept->getDefaultLanguage(), $entityManager);
        $concept->setIsValidated(true);
        $entityManager->persist($concept);
        $entityManager->flush();
        return $this->redirectToRoute('app_concept_show', [
            'title' => $concept->getTitle(),
        ]);
    }


}
