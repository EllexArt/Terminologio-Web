<?php

namespace App\Service;

use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Repository\ComponentNameRepository;
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

   public function calculateComponentsWithDefaultTrad(Concept $concept) : array {
       $componentsTrad = [];
       foreach ($concept->getComposants() as $component) {
           $trads = $component->getComposantNames();
           $default_trad = null;
           foreach ($trads as $trad) {
               if ($trad->getLanguage()->getName() == $concept->getDefaultLanguage()->getName()) {
                   $default_trad = $trad;
               }
           }

           $componentTrad = new ComponentTrad(
               $component->getId(),
               $component->getNumber(),
               $default_trad == null ? "" : $default_trad->getValue(),
               $component->getPositionX(),
               $component->getPositionY()
           );
           $componentsTrad[] = $componentTrad;
       }
       return $componentsTrad;
   }

}