<?php

namespace App\Controller;

use App\Entity\Composant;
use App\Entity\ComposantName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Form\ConceptUploadType;
use App\Repository\ComposantNameRepository;
use App\Repository\ComposantRepository;
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

    #[Route('/concept/{title}/component', name: 'app_concept_component_edit')]
    public function editComponent(ConceptService $conceptService, ComposantNameRepository $composantNameRepository, Concept $concept): Response
    {
        return $this->render('concept/edit_component.html.twig', [
            'imagePath' => 'uploads/images/' . $concept->getImage(),
            'components' => $conceptService->calculateComponentsWithDefaultTrad($concept),
            'concept' => $concept
        ]);
    }

    #[Route('/concept/{title}/component/add/{horizontal_position}/{vertical_position}', name: 'app_concept_component_add')]
    public function addComponent(ConceptService $conceptService, ComposantNameRepository $composantNameRepository, ComposantRepository $composantRepository, EntityManagerInterface $entityManager, Concept $concept, int $horizontal_position, int $vertical_position): Response
    {
        $component = new Composant();
        $component->setConcept($concept);
        $component->setNumber($composantRepository->calculateNextNumber($concept));
        $component->setPositionX($horizontal_position);
        $component->setPositionY($vertical_position);

        $componentName = new ComposantName();
        $componentName->setComposant($component);
        $componentName->setLanguage($concept->getDefaultLanguage());
        $componentName->setValue("Composant".$component->getNumber());
        $entityManager->persist($componentName);

        $component->addComposantName($componentName);

        $entityManager->persist($component);

        $entityManager->flush();

        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);
        $componentsTrad[] = new ComponentTrad($component->getId(),
                                                $component->getNumber(),
                                                "",
                                                $component->getPositionX(),
                                                $component->getPositionY());

        return $this->render('concept/components.html.twig', [
            'components' => $componentsTrad
        ]);
    }

    #[Route('/concept/{title}/component/delete/{id}', name: 'app_concept_component_delete')]
    public function deleteComponent(ConceptService $conceptService,
        ComposantNameRepository $composantNameRepository,
        EntityManagerInterface $entityManager,
        #[MapEntity(mapping: ['title' => 'title'])] Concept $concept,
        #[MapEntity(id: 'id')] Composant $composant_to_delete): Response
    {
        if($composant_to_delete->getConcept() == $concept) {
            $entityManager->remove($composant_to_delete);

            foreach ($concept->getComposants() as $composant) {
                if($composant->getNumber() > $composant_to_delete->getNumber())
                $composant->setNumber($composant->getNumber() - 1);
                $entityManager->persist($composant);
            }

            $entityManager->flush();
        }
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);

        return $this->render('concept/components.html.twig', [
            'components' => $componentsTrad
        ]);
    }

    #[Route('/concept/{title}/component/buttons', name: 'app_concept_component_buttons')]
    public function getComponentsButtons(ConceptService $conceptService,
        ComposantNameRepository $composantNameRepository,
        Concept $concept): Response
    {
        $componentsTrad = $conceptService->calculateComponentsWithDefaultTrad($concept);

        return $this->render('concept/components_edit.html.twig', [
            'components' => $componentsTrad
        ]);
    }

    #[Route('/concept/{title}/validate', name: 'app_concept_validate')]
    public function validateConcept(Concept $concept, Request $request): Response
    {
        $number = 0;
        $components = $concept->getComposants();
        $trads[] = $components[$number]->getComposantNames();
        while (($trad = $request->get('componentText'.$number)) != null) {

        }

        return $this->redirectToRoute('app_terminologio_index');
    }

}
