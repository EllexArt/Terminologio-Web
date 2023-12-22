<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\ConceptRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, ConceptRepository $conceptRepository): Response
    {
        return $this->render('admin/admin_management.html.twig', [
            'users' => $userRepository->findByRoleUser(),
            'concepts' => $conceptRepository->findAll(),
        ]);
    }

    #[Route('/delete/user/{id}', name: 'app_delete_user')]
    public function deleteUser(EntityManagerInterface $entityManager, User $user) : Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/delete/concept/{id}', name: 'app_delete_concept')]
    public function deleteConcept(EntityManagerInterface $entityManager, Concept $concept) : Response
    {
        $entityManager->remove($concept);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/add/user', name:'app_add_user')]
    public function addUser() : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        return $this->render('admin/admin_add_user.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }
}
