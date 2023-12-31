<?php

namespace App\Controller;


use App\Entity\DTO\PasswordEditor;
use App\Entity\DTO\ProfileEditor;
use App\Form\ChangePasswordFormType;
use App\Form\ChangeProfileFieldFormType;
use App\Repository\UserRepository;
use App\Service\UploadImageService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController  extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        return $this->render('profile/profile.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function deleteAccount(EntityManagerInterface $entityManager,
                                  UploadImageService $uploadImageService,
                                  UserService $userService): Response
    {
        $user = $this->getUser();
        if(in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('warning', "Impossible to delete admin account");
            return $this->redirectToRoute('app_profile');
        }
        foreach ($user->getConcepts() as $concept) {
            if(!$concept->isIsValidated()) {
                $uploadImageService->deleteImage($this->getParameter('image_directory'), $concept->getImage());
                $entityManager->remove($concept);
            } else {
                $concept->setAuthor(null);
                $entityManager->persist($concept);
            }
        }
        $userService->deleteAccount($user);
        $session = new Session();
        $session->invalidate();
        $this->addFlash('info', 'Your account has been successfully deleted');
        return $this->redirectToRoute('app_logout');
    }

    #[Route('/profile/username', name: 'app_profile_username')]
    public function selectNewUsername(Request $request, UserRepository $userRepository,
                                     UserService $userService): Response
    {
        $profileEditor = new ProfileEditor();
        $form = $this->createForm(ChangeProfileFieldFormType::class, $profileEditor);
        $form->handleRequest($request);
        $this->handleErrors($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $profileEditor->getFieldToEdit();

            if($userRepository->findOneBy(['username' => $username])) {
                $this->addFlash('warning', 'Username already taken');
                return $this->render('profile/changeProfileData.html.twig', [
                    'field' => 'username',
                    'form' => $form->createView()
                ]);
            }

            $user = $this->getUser();
            $userService->changeUsername($user, $username);
            $this->addFlash('info', 'Username changed');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/changeProfileData.html.twig', [
            'field' => 'username',
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/email', name: 'app_profile_email')]
    public function selectNewEmail(Request $request, UserRepository $userRepository,
                                   UserService $userService): Response
    {
        $profileEditor = new ProfileEditor();
        $form = $this->createForm(ChangeProfileFieldFormType::class, $profileEditor);
        $form->handleRequest($request);
        $this->handleErrors($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $mail = $profileEditor->getFieldToEdit();

            if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('warning', 'Invalid email');
                return $this->render('profile/changeProfileData.html.twig', [
                    'field' => 'email',
                    'form' => $form->createView()
                ]);
            }

            if($userRepository->findOneBy(['email' => $mail])) {
                $this->addFlash('warning', 'Email already taken');
                return $this->render('profile/changeProfileData.html.twig', [
                    'field' => 'email',
                    'form' => $form->createView()
                ]);
            }

            $user = $this->getUser();
            $userService->changeEmail($user, $mail);
            $this->addFlash('info', 'Email changed');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/changeProfileData.html.twig', [
            'field' => 'email',
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/password', name: 'app_profile_password')]
    public function selectNewPassword(UserPasswordHasherInterface $userPasswordHasher, Request $request,
                                      UserService $userService): Response
    {
        $passwordEditor = new PasswordEditor();
        $form = $this->createForm(ChangePasswordFormType::class, $passwordEditor);
        $form->handleRequest($request);
        $this->handleErrors($form);
        if ($form->isSubmitted() && $form->isValid()) {
            if($passwordEditor->getNewPassword() != $passwordEditor->getConfirmNewPassword()) {
                $this->addFlash('warning', "Password confirmation failed, please retry");
                return $this->render('profile/changePassword.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            $user = $this->getUser();
            if(!$userPasswordHasher->isPasswordValid($user, $passwordEditor->getOldPassword())) {
                $this->addFlash('warning', "Invalid password, please retry");
                return $this->render('profile/changePassword.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            $userService->changePassword($user, $userPasswordHasher, $passwordEditor);

            $this->addFlash('info', 'Password changed');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/changePassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function handleErrors(FormInterface $form): void
    {
        if($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('warning', $error->getMessage());
            }
        }
    }

}