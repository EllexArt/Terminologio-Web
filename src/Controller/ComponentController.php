<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\Concept;
use App\Repository\ComponentRepository;
use App\Service\ComponentService;
use App\Service\ConceptService;
use App\Service\UploadImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ComponentController extends AbstractController
{

    #[Route('/concept/{title}/component/add/{horizontal_position}/{vertical_position}', name: 'app_concept_component_add', methods: 'POST')]
    #[IsGranted('ROLE_USER')]
    public function addComponentToConcept(ComponentRepository $ComponentRepository,
        EntityManagerInterface $entityManager, ComponentService $componentService,
        Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
        if(($response = $this->isValidDraftForUser($concept)) != null) {
            return $response;
        }
        $componentService->createComponent($ComponentRepository, $concept,
                                            $horizontal_position, $vertical_position);
        return $this->render('concept/components/components_number_block.html.twig', [
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/component/delete/{id}', name: 'app_concept_component_delete', methods: 'POST')]
    public function deleteComponentOfConcept(
        EntityManagerInterface $entityManager,
        UploadImageService $uploadImageService,
        ComponentService $componentService,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Component $componentToDelete = null): Response
    {
        if(!$this->isGranted('ROLE_USER') and !$this->isGranted('ROLE_ADMIN')) {
            return new Response('Forbidden', 403);
        }
        $user = $this->getUser();
        if(!$this->isGranted('ROLE_ADMIN', $user) && ($response = $this->isValidDraftForUser($concept)) != null) {
            return $response;
        }

        if($componentToDelete == null || $componentToDelete->getConcept()->getId() != $concept->getId()) {
            $this->addFlash('warning', 'Impossible to delete this component. Maybe it has already been deleted ?');
            return new Response('Invalid component', 404);
        }
        $result = $componentService->removeComponent($concept, $componentToDelete, $this->isGranted('ROLE_ADMIN'));
        if($result == 1) {
            $this->addFlash('info', 'No more components, concept deleted');
            $uploadImageService->deleteImage($this->getParameter('image_directory'), $concept->getImage());
            return new Response('Concept deleted', 204);
        }
        return $this->render('concept/components/components_number_block.html.twig', [
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/component/legend', name: 'app_concept_component_buttons', methods: 'POST')]
    public function getComponentsLegend(ConceptService $conceptService,
        Concept $concept): Response
    {
        if(!$this->isGranted('ROLE_USER') and !$this->isGranted('ROLE_ADMIN')) {
            return new Response('Forbidden', 403);
        }
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);

        return $this->render('concept/components/components_legend_block.html.twig', [
            'components' => $componentsTrad
        ]);
    }


    #[Route('/concept/{title}/component/styles', name: 'app_concept_styles', methods: 'POST')]
    public function getStyleOfComponents( ConceptService $conceptService, Concept $concept): Response
    {
        if(!$this->isGranted('ROLE_USER') and !$this->isGranted('ROLE_ADMIN')) {
            return new Response('Forbidden', 403);
        }
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/components/hover_component_stylesheets_block.html.twig', [
            'components' => $componentsTrad
        ]);
    }


    private function isValidDraftForUser(Concept $concept) : ?Response{
        $user = $this->getUser();
        if($concept->getAuthor()->getId() != $user->getId()) {
            $this->addFlash('warning', 'Impossible to edit this draft, it\'s not yours');
            return $this->redirectToRoute('app_terminologio_index');
        }

        if($concept->isIsValidated()) {
            $this->addFlash('warning', 'This concept can no longer be edited');
            return $this->redirectToRoute('app_terminologio_index');
        }
        return null;
    }

}
