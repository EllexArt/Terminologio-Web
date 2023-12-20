<?php

namespace App\Controller;

use App\Entity\Composant;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Form\ConceptUploadType;
use App\Repository\ComposantNameRepository;
use App\Repository\ComposantRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptController extends AbstractController
{
    #[Route('/concept/upload', name: 'app_concept_upload')]
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
        return $this->render('concept/upload.html.twig', [
            'uploadForm' => $form->createView(),
        ]);
    }

    #[Route('/concept/{title}/component/add', name: 'app_concept_component_edit')]
    public function editComponent(LoggerInterface $logger, ComposantNameRepository $composantNameRepository, Concept $concept): Response
    {
        $componentsTrad = [];
        foreach ($concept->getComposants() as $component) {
            $trad = $composantNameRepository->findOneBy(['composant' => $component]);
            $componentTrad = new ComponentTrad(
                $component->getNumber(),
                $trad == null ? "" : $trad,
                $component->getPositionX(),
                $component->getPositionY()
            );
            $logger->info($componentTrad->getPositionY());
            $componentsTrad[] = $componentTrad;
        }
        return $this->render('concept/edit_component.html.twig', [
            'imagePath' => 'uploads/images/' . $concept->getImage(),
            'components' => $componentsTrad,
            'concept' => $concept
        ]);
    }

    #[Route('/concept/{title}/component/add/{horizontal_position}/{vertical_position}', name: 'app_concept_component_add')]
    public function addComponent(ComposantRepository $composantRepository, EntityManagerInterface $entityManager, Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
        $component = new Composant();
        $component->setConcept($concept);
        $component->setNumber($composantRepository->calculateNextNumber($concept));
        $component->setPositionX($horizontal_position);
        $component->setPositionY($vertical_position);
        $entityManager->persist($component);
        $entityManager->flush();

        return $this->redirectToRoute('app_concept_component_edit', [
            'title' => $concept->getTitle()]);
    }
}
