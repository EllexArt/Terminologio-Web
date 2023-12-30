<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Repository\ComponentRepository;
use App\Service\ConceptService;
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
        EntityManagerInterface $entityManager,
        Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
        if(($response = $this->isValidDraftForUser($concept)) != null) {
            return $response;
        }

        $component = new Component();
        $component->setConcept($concept);
        $component->setNumber($ComponentRepository->calculateNextNumber($concept));
        $component->setPositionX($horizontal_position);
        $component->setPositionY($vertical_position);
        $concept->addComponent($component);

        $componentName = new ComponentName();
        $componentName->setComponent($component);
        $componentName->setLanguage($concept->getDefaultLanguage());
        $componentName->setValue("");
        $component->addComponentName($componentName);

        $entityManager->persist($componentName);
        $entityManager->persist($component);

        $entityManager->flush();

        return $this->render('concept/components/components_number_block.html.twig', [
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/component/delete/{id}', name: 'app_concept_component_delete', methods: 'POST')]
    public function deleteComponentOfConcept(
        EntityManagerInterface $entityManager,
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
        $concept->removeComponent($componentToDelete);
        $entityManager->remove($componentToDelete);
        $entityManager->persist($concept);

        foreach ($concept->getComponents() as $component) {
            if ($component->getNumber() > $componentToDelete->getNumber()) {
                $component->setNumber($component->getNumber() - 1);
                $entityManager->persist($component);
            }
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            if(sizeof($concept->getComponents()) == 0) {
                $entityManager->remove($concept);
                $this->addFlash('info', 'No more components, concept deleted');
                $entityManager->flush();
                return new Response('Concept deleted', 204);
            }
        }

        $entityManager->flush();
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
