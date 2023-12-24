<?php

namespace App\Controller;

use App\Entity\Composant;
use App\Entity\ComposantName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Form\ConceptUploadType;
use App\Repository\ComposantNameRepository;
use App\Repository\ComposantRepository;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptController extends AbstractController
{
    #[Route('/concept/create', name: 'app_concept_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager, ConceptService $conceptService, SluggerInterface $slugger): Response
    {
        $concept = new Concept();
        $form = $this->createForm(ConceptUploadType::class, $concept);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $conceptService->uploadConcept($image, $slugger, $concept, $entityManager, $this->getParameter('image_directory'));

            return $this->redirectToRoute('app_concept_component_edit', [
                'title' => $concept->getTitle()]);
        }
        return $this->render('concept/create_concept.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }

    #[Route('/concept/{title}/validate', name: 'app_concept_validate', methods: 'POST')]
    public function validateConcept(ComposantNameRepository $composantNameRepository, EntityManagerInterface $entityManager, Concept $concept, Request $request): Response
    {
        $number = 0;
        $components = $concept->getComposants();
        while (($trad = $request->get('componentText'.$number)) != null) {
            $component_name = $composantNameRepository
                ->getComponentNameFromComponentAndLanguage($components[$number], $concept->getDefaultLanguage());
            $component_name->setValue($trad);
            $entityManager->persist($component_name);
            $number++;
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_concept_list');
    }

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
        return $this->render('concept/show_concept.html.twig', [
            'concept' => $concept,
            'componentsName' => $componentsTrad,
        ]);
    }


    #[Route('/concept/{title}/translate', name: 'app_concept_translation')]
    public function createOrEditTranslation(LanguageRepository $languageRepository, ConceptService $conceptService, Concept $concept) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/translation/add_translation.html.twig', [
            'concept' => $concept,
            'componentsName' => $componentsTrad,
            'languages' => $languageRepository->findAll()
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
}
