<?php

namespace App\Controller;

use App\Entity\Component;
use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Repository\ComponentNameRepository;
use App\Repository\ComponentRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ComponentController extends AbstractController
{

    #[Route('/concept/{title}/component/add/{horizontal_position}/{vertical_position}', name: 'app_concept_component_add', methods: 'POST')]
    public function addComponent(ComponentRepository $ComponentRepository,
        EntityManagerInterface $entityManager,
        Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
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
    public function deleteComponent(
        EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Component $componentToDelete = null): Response
    {
        if($componentToDelete == null || $componentToDelete->getConcept() != $concept) {
            $this->addFlash('warning', 'Impossible to delete this component. Maybe it has already been deleted ?');
            return new Response('Invalid component', 404);
        }
        $concept->removeComponent($componentToDelete);
        $entityManager->remove($componentToDelete);

        foreach ($concept->getComponents() as $component) {
            if ($component->getNumber() > $componentToDelete->getNumber()) {
                $component->setNumber($component->getNumber() - 1);
                $entityManager->persist($component);
            }
        }

        $entityManager->flush();
        return $this->render('concept/components/components_number_block.html.twig', [
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/component/buttons', name: 'app_concept_component_buttons', methods: 'POST')]
    public function getComponentsButtons(ConceptService $conceptService,
        Concept $concept): Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);

        return $this->render('concept/components/components_add_block.html.twig', [
            'components' => $componentsTrad,
        ]);
    }


    #[Route('/concept/{title}/component/get/{name}', name: 'app_concept_components_trad')]
    public function getComponentToShow(ConceptService $conceptService, Concept $concept, Language $language) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithTrad($concept, $language);
        return $this->render('concept/show/components_show_block.html.twig', [
            'componentsName' => $componentsTrad,
        ]);
    }

    #[Route('/concept/{title}/component/styles', name: 'app_concept_styles', methods: 'POST')]
    public function getStyleOfComponents( ConceptService $conceptService, Concept $concept): Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        return $this->render('concept/components/hover_component_block.html.twig', [
            'components' => $componentsTrad
        ]);
    }

}
