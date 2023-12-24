<?php

namespace App\Controller;

use App\Entity\Composant;
use App\Entity\ComposantName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Repository\ComposantNameRepository;
use App\Repository\ComposantRepository;
use App\Service\ConceptService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComponentController extends AbstractController
{

    #[Route('/concept/{title}/component', name: 'app_concept_component_edit')]
    public function addComponentsToConcept(ConceptService $conceptService, Concept $concept): Response
    {
        return $this->render('concept/edit_concept.html.twig', [
            'components' => $conceptService->calculateComponentsWithDefaultTrad($concept),
            'concept' => $concept,
        ]);
    }

    #[Route('/concept/{title}/component/add/{horizontal_position}/{vertical_position}', name: 'app_concept_component_add', methods: 'POST')]
    public function addComponent(ComposantRepository $composantRepository,
        EntityManagerInterface $entityManager,
        Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
        $component = new Composant();
        $component->setConcept($concept);
        $component->setNumber($composantRepository->calculateNextNumber($concept));
        $component->setPositionX($horizontal_position);
        $component->setPositionY($vertical_position);
        $concept->addComposant($component);

        $componentName = new ComposantName();
        $componentName->setComposant($component);
        $componentName->setLanguage($concept->getDefaultLanguage());
        $componentName->setValue("");
        $component->addComposantName($componentName);

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
        #[MapEntity(id: 'id')] Composant $componentToDelete): Response
    {
        $precedentId = $componentToDelete->getId();
        if($componentToDelete->getConcept() == $concept) {
            $concept->removeComposant($componentToDelete);
            $entityManager->remove($componentToDelete);

            foreach ($concept->getComposants() as $component) {
                if($component->getNumber() > $componentToDelete->getNumber()) {
                    $component->setNumber($component->getNumber() - 1);
                    $entityManager->persist($component);
                }
            }

            $entityManager->flush();
        }

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
    public function showConcept(ConceptService $conceptService, Concept $concept, Language $language) : Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithTrad($concept, $language);
        return $this->render('concept/components/components_show.html.twig', [
            'componentsName' => $componentsTrad,
        ]);
    }

}