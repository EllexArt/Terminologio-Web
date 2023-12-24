<?php

namespace App\Service;

use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Entity\Language;
use App\Repository\ComposantNameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConceptService
{

   public function uploadConcept(mixed $image,
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
       $entityManager->persist($concept);
       $entityManager->flush();
   }

    public function calculateComponentsWithTrad(Concept $concept, Language $language) : array {
        $componentsTrad = [];
        foreach ($concept->getComposants() as $component) {
            $componentNameGoodLanguage = null;
            foreach ($component->getComposantNames() as $componentName) {
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

}