<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Repository\CategoryRepository;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Service\ConceptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConceptShowController extends AbstractController
{

    #[Route('/concept/list/', name: 'app_concept_list')]
    public function listConcepts(ConceptRepository $conceptRepository,
        LanguageRepository $languageRepository,
        CategoryRepository $categoryRepository,
        ConceptService $conceptService,
        Request $request) : Response
    {
        $categoryId = ($request->query->get('category') == null ? -1 : $request->query->get('category'));
        $languageId = ($request->query->get('language') == null ? -1 : $request->query->get('language'));
        $concepts = $conceptRepository->findBy(['isValidated' => true]);
        $concepts = $conceptService->getConceptsToShow($concepts, $categoryId, $languageId, -1);

        return $this->render('concept/list/list_concepts.html.twig',
            [
                'concepts' => $concepts,
                'languages' => $languageRepository->findAll(),
                'categories' => $categoryRepository->findAll(),
            ]);
    }


    #[Route('/concept/{title}/show', name: 'app_concept_show')]
    public function showConcept(ConceptService $conceptService, Concept $concept) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/show/show_concept.html.twig', [
            'concept' => $concept,
            'components' => $componentsTrad
        ]);
    }
}