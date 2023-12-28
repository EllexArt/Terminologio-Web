<?php

namespace App\Controller;

use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Entity\Language;
use App\Repository\CategoryRepository;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Service\ConceptService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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
        Request $request) : Response
    {
        $categoryId = $request->query->get('category');
        $languageId = $request->query->get('language');
        $concepts = $conceptRepository->findBy(['isValidated' => true]);
        for ($i = sizeof($concepts) - 1; $i >= 0 ; $i--) {
            $languagesOfConcept = array_map( fn(ComponentName $componentName): int => $componentName->getLanguage()->getId() ,
                $concepts[$i]->getComponents()[0]->getComponentNames()->toArray());
            if (($concepts[$i]->getCategory()->getId() <> $categoryId and $categoryId != -1)
                or ($concepts[$i]->getDefaultLanguage()->getId() <> $languageId
                and !in_array($languageId, $languagesOfConcept) and $languageId != -1 )) {
                array_splice($concepts, $i, 1);
            }
        }
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
            'components' => $componentsTrad,
        ]);
    }
}