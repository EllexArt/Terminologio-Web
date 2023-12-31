<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Repository\CategoryRepository;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Service\ConceptService;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WorkspaceController extends AbstractController
{

    #[Route('/concept/drafts/', name: 'app_concept_drafts')]
    #[IsGranted('ROLE_USER')]
    public function listDraftsOfUser(ConceptRepository $conceptRepository,
                                 LanguageRepository $languageRepository,
                                 CategoryRepository $categoryRepository,
                                 ConceptService $conceptService,
                                 Request $request) : Response
    {
        $categoryId = ($request->query->get('category') == null ? -1 : $request->query->get('category'));
        $languageId = ($request->query->get('language') == null ? -1 : $request->query->get('language'));
        $user = $this->getUser();
        $concepts = $conceptRepository->findBy(['isValidated' => false]);
        $concepts = $conceptService->getConceptsToShow($concepts, $categoryId, $user->getId());

        return $this->render('concept/drafts/list_drafts.html.twig',
            [
                'concepts' => $concepts,
                'languages' => $languageRepository->findAll(),
                'categories' => $categoryRepository->findAll(),
            ]);
    }

    #[Route('/concept/drafts/delete/{id}', name: 'app_concept_drafts_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteDraft(Concept $concept, EntityManagerInterface $entityManager,
                                UploadImageService $uploadImageService) : Response
    {
        $user = $this->getUser();
        if($concept->getAuthor()->getId() != $user->getId()) {
            $this->addFlash('warning', 'This is not your draft, impossible to delete');
            return $this->redirectToRoute('app_concept_drafts');
        }
        if($concept->isIsValidated()) {
            $this->addFlash('warning', 'This is not a draft, impossible to delete');
            return $this->redirectToRoute('app_concept_drafts');
        }
        $filename = $concept->getImage();
        $entityManager->remove($concept);
        $entityManager->flush();
        $uploadImageService->deleteImage($this->getParameter('image_directory'), $filename);
        return $this->redirectToRoute('app_concept_drafts');
    }
}