<?php

namespace App\Service;

use App\Entity\Component;
use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Repository\ComponentRepository;
use Doctrine\ORM\EntityManagerInterface;

class ComponentService
{

    private EntityManagerInterface $entityManager;

    //CONSTRUCTOR
    function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    //COMMANDS
    public function createComponent(ComponentRepository $ComponentRepository,
                                    Concept $concept,
                                    int $horizontal_position,
                                    int $vertical_position) : void
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

        $this->entityManager->persist($componentName);
        $this->entityManager->persist($component);

        $this->entityManager->flush();
    }

    public function removeComponent(Concept $concept,
                                    Component $componentToDelete, bool $isAdmin) : int
    {
        $concept->removeComponent($componentToDelete);
        $this->entityManager->remove($componentToDelete);
        $this->entityManager->persist($concept);

        foreach ($concept->getComponents() as $component) {
            if ($component->getNumber() > $componentToDelete->getNumber()) {
                $component->setNumber($component->getNumber() - 1);
                $this->entityManager->persist($component);
            }
        }

        if ($isAdmin) {
            if(sizeof($concept->getComponents()) == 0) {
                $this->entityManager->remove($concept);
                $this->entityManager->flush();
                return 1;
            }
        }

        $this->entityManager->flush();
        return 0;
    }
}