<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\Language;
use App\Repository\ComposantNameRepository;
use App\Repository\LanguageRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TranslationController extends AbstractController
{
    #[Route('/concept/{title}/translate', name: 'app_concept_translation')]
    public function createOrEditTranslation(LanguageRepository $languageRepository, ConceptService $conceptService, Concept $concept) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/translation/add_translation.html.twig', [
            'concept' => $concept,
            'componentsName' => $componentsTrad,
            'languages' => $languageRepository->findAll(),
        ]);
    }

    #[Route('/concept/{title}/translate/get/{id}', name: 'app_concept_translation_get_language')]
    public function getTranslationFromConcept(ConceptService $conceptService,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Language $language) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithTrad($concept, $language);
        return $this->render('concept/translation/components_translate_block.html.twig', [
            'concept' => $concept,
            'componentsName' => $componentsTrad,
        ]);
    }


    #[Route('/concept/{title}/translate/save/{id}', name: 'app_concept_translation_save')]
    public function saveTranslation(ConceptService $conceptService, ComposantNameRepository $composantNameRepository,
        EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Language $language,
        Request $request) : Response
    {
        $conceptService->saveComponentNames($concept, $request, $composantNameRepository, $language, $entityManager);
        return $this->redirectToRoute('app_concept_show', [
            'title' => $concept->getTitle(),
        ]);
    }
}