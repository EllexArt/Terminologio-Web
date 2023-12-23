<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\Language;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, ConceptRepository $conceptRepository,
                          LanguageRepository $languageRepository): Response
    {
        return $this->render('admin/admin_management.html.twig', [
            'users' => $userRepository->findByRoleUser(),
            'concepts' => $conceptRepository->findAll(),
            'languages' => $languageRepository->findAll(),
            'success' => false,
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
    public function addUser(LoggerInterface $logger, Request $request, UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface $entityManager,
                            UserRepository $userRepository,
                            ConceptRepository $conceptRepository,
                            LanguageRepository $languageRepository) : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setRoles($user->getRoles());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('admin/admin_management.html.twig', [
                'users' => $userRepository->findByRoleUser(),
                'concepts' => $conceptRepository->findAll(),
                'languages' => $languageRepository->findAll(),
                'success' => true,
            ]);
        }
        return $this->render('admin/admin_add_user.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/language/{id}', name: 'app_delete_language')]
    public function deleteLanguage(EntityManagerInterface $entityManager, Language $language) : Response
    {
        $entityManager->remove($language);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/add/language', name:'app_add_language')]
    public function addLanguage(LoggerInterface $logger, Request $request, UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface $entityManager,
                            UserRepository $userRepository,
                            ConceptRepository $conceptRepository,
                            LanguageRepository $languageRepository) : Response
    {
        $language = new Language();
        return $this->redirectToRoute('app_admin');
    }
}
