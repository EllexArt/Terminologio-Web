<?php

namespace App\Service;

use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Entity\User;
use App\Repository\ComponentNameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptService
{

    public function uploadConcept(mixed $image,
        User $user,
        SluggerInterface $slugger,
        Concept $concept,
        EntityManagerInterface $entityManager,
        string $directory): void
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

        try {
            $image->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {

        }

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

}
