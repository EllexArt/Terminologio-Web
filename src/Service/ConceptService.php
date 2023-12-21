<?php

namespace App\Service;

use App\Entity\Concept;
use App\Entity\DTO\ComponentTrad;
use App\Repository\ComposantNameRepository;
use Doctrine\ORM\EntityManagerInterface;
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

   public function calculateComponentsWithDefaultTrad(ComposantNameRepository $composantNameRepository, Concept $concept) : array {
       $componentsTrad = [];
       foreach ($concept->getComposants() as $component) {
           $trad = $composantNameRepository->findOneBy(['composant' => $component]);
           $componentTrad = new ComponentTrad(
               $component->getNumber(),
               $trad == null ? "" : $trad,
               $component->getPositionX(),
               $component->getPositionY()
           );
           $componentsTrad[] = $componentTrad;
       }
       return $componentsTrad;
   }

}