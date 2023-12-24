<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\Language;
use App\Repository\ConceptRepository;
use App\Service\ConceptService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConceptShowController extends AbstractController
{
    #[Route('/concept/list', name: 'app_concept_list')]
    public function listConcepts(ConceptRepository $conceptRepository) : Response
    {
        $concepts = $conceptRepository->findAll();
        return $this->render('concept/list_concepts.html.twig',
            [
                'concepts' => $concepts,
            ]);
    }


    #[Route('/concept/{title}/show', name: 'app_concept_show')]
    public function showConcept(ConceptService $conceptService, Concept $concept) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/show/show_concept.html.twig', [
            'concept' => $concept,
            'componentsName' => $componentsTrad,
        ]);
    }

    #[Route('/concept/{title}/show/{id}/components/get', name: 'app_concept_show_get_components', methods: 'POST')]
    public function getComponentsToShow(ConceptService $conceptService,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Language $language) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithTrad($concept, $language);
        return $this->render('concept/show/components_show.html.twig', [
            'componentsName' => $componentsTrad,
        ]);
    }
}