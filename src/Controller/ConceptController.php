<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Form\ConceptUploadType;
use App\Repository\ComponentNameRepository;
use App\Repository\ConceptRepository;
use App\Service\ConceptService;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_USER')]
class ConceptController extends AbstractController
{
    #[Route('/concept/create', name: 'app_concept_upload')]
    public function create(Request $request, EntityManagerInterface $entityManager,
                           ConceptService $conceptService, SluggerInterface $slugger,
                           ConceptRepository $conceptRepository, UploadImageService $uploadImageService): Response
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

            if($concept->getTitle() == "create" || $concept->getTitle() == "list" || $concept->getTitle() == "drafts") {
                $this->addFlash('warning', 'Invalid title, please choose another');
                return $this->render('concept/create_concept.html.twig', [
                    'uploadForm' => $form->createView(),
                ]);
            }

            $image = $form->get('image')->getData();
            $user = $this->getUser();

            $newFilename = $uploadImageService->uploadImage($slugger, $image, $this->getParameter('image_directory'));
            if($newFilename == null) {
                $this->addFlash('Warning', 'Issue while uploading file');
                return $this->render('concept/create_concept.html.twig', [
                    'uploadForm' => $form->createView(),
                ]);
            }
            $conceptService->uploadConcept($user, $concept, $entityManager, $newFilename);

            $this->addFlash('info', 'Concept created, edit your draft');
            return $this->redirectToRoute('app_concept_component', [
                'title' => $concept->getTitle()]);
        }
        return $this->render('concept/create_concept.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }

    #[Route('/concept/{title}/component', name: 'app_concept_component')]
    public function editDraft(ConceptService $conceptService, Concept $concept): Response
    {
        if(($response = $this->isValidDraftForUser($concept)) != null) {
            return $response;
        }
        return $this->render('concept/add_component_to_concept.html.twig', [
            'components' => $conceptService->calculateComponentsWithDefaultTrad($concept),
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/validate', name: 'app_concept_validate', methods: 'POST')]
    public function validateConcept(ConceptService $conceptService, ComponentNameRepository $componentNameRepository, EntityManagerInterface $entityManager, Concept $concept, Request $request): Response
    {
        if(($response = $this->isValidDraftForUser($concept)) != null) {
            return $response;
        }
        if(sizeof($concept->getComponents()) == 0) {
            $this->addFlash('warning', 'You need to specify at least one component');
            return $this->redirectToRoute('app_concept_component', [
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

    private function isValidDraftForUser(Concept $concept) : ?Response {
        $user = $this->getUser();
        if($concept->getAuthor()->getId() != $user->getId()) {
            $this->addFlash('warning', "Impossible to edit this draft, it's not yours");
            return $this->redirectToRoute('app_terminologio_index');
        }

        if($concept->isIsValidated()) {
            $this->addFlash('warning', "Concept has been validated, impossible to edit or validate");
            return $this->redirectToRoute('app_concept_show', [
                'title' => $concept->getTitle(),
            ]);
        }
        return null;
    }


}
