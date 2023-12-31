<?php

namespace App\Service;

use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Entity\User;
use App\Repository\ComponentNameRepository;
use App\Repository\ConceptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptService
{

    public function uploadConcept(User $user, Concept $concept,
                                  EntityManagerInterface $entityManager,
                                  string $newFilename
    ): void
    {
        $concept->setImage($newFilename);
        $concept->setAuthor($user);
        $concept->setIsValidated(false);
        $entityManager->persist($concept);
        $entityManager->flush();
    }

    public function calculateComponentsWithTrad(Concept $concept, Language $language) : array {
        $componentsTrad = [];
        foreach ($concept->getComponents() as $component) {
            $componentNameGoodLanguage = null;
            foreach ($component->getComponentNames() as $componentName) {
                if ($componentName->getLanguage()->getName() == $language->getName()) {
                    $componentNameGoodLanguage = $componentName;
                }
            }

            $componentTrad = new ComponentTrad(
                $component->getId(),
                $component->getNumber(),
                $componentNameGoodLanguage == null ? "" : $componentNameGoodLanguage->getValue(),
                $component->getPositionX(),
                $component->getPositionY()
            );
            $componentsTrad[] = $componentTrad;
        }
        return $componentsTrad;
    }

    public function calculateComponentsWithDefaultTrad(Concept $concept) : array {
        return $this->calculateComponentsWithTrad($concept, $concept->getDefaultLanguage());
    }

    public function saveComponentNames(Concept $concept,
        Request $request,
        ComponentNameRepository $ComponentNameRepository,
        Language $language,
        EntityManagerInterface $entityManager): void
    {
        $number = 0;
        $components = $concept->getComponents();
        while (($trad = $request->get('componentText'.$number)) != null) {
            $component_name = $ComponentNameRepository
                ->getComponentNameFromComponentAndLanguage($components[$number], $language);
            if($component_name == null) {
                $component_name = new ComponentName();
                $component_name->setLanguage($language);
                $component_name->setComponent($components[$number]);
            }
            $component_name->setValue($trad);
            $entityManager->persist($component_name);
            $number++;
        }
        $entityManager->flush();
    }




    public function getConceptsToShow(ConceptRepository $conceptRepository, int $categoryNumber, int $languageNumber, int $userId): array
    {
        $concepts = $conceptRepository->findAll();
        for ($i = sizeof($concepts) - 1; $i >= 0 ; $i--) {
            if ($this->isConceptNotInCategory($concepts[$i], $categoryNumber)
                or $this->isConceptNotTranslated($concepts[$i], $categoryNumber)
                or !$this->isUserAuthorOfConcept($concepts[$i], $userId)) {
                array_splice($concepts, $i, 1);
            }
        }
        return $concepts;
    }

    private function isConceptNotInCategory(Concept $concept, int $categoryId): bool
    {
        return $concept->getCategory()->getId() <> $categoryId and $categoryId != -1;
    }

    private function isConceptNotTranslated(Concept $concept, int $languageId): bool
    {
        $firstComponent = $concept->getComponents()[0];
        if($firstComponent == null) {
            return $concept->getDefaultLanguage()->getId() <> $languageId and $languageId != -1;
        }
        $languagesOfConcept = array_map( fn(ComponentName $componentName): int => $componentName->getLanguage()->getId() ,
            $firstComponent->getComponentNames()->toArray());
        return ($concept->getDefaultLanguage()->getId() <> $languageId
            and !in_array($languageId, $languagesOfConcept) and $languageId != -1 );
    }

    private function isUserAuthorOfConcept(Concept $concept, int $userId): bool
    {
        return $userId == -1 or $concept->getAuthor()->getId() == $userId;
    }

}
