<?php

namespace App\Service;

use App\Entity\DTO\PasswordEditor;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private EntityManagerInterface $entityManager;

    //CONSTRUCTOR
    function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    //REQUESTS
    public function isValidPassword(FormInterface $form) : bool
    {
        return $form->get('plainPassword')->getData() != $form->get('confirmPassword')->getData();
    }

    //COMMANDS
    public function register(User $user, UserPasswordHasherInterface $userPasswordHasher
        , FormInterface $form) : void
    {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->addRole("ROLE_USER");
            $this->entityManager->persist($user);
            $this->entityManager->flush();
    }

    public function deleteAccount(?User $user) : void
    {
        if ($user != null) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    public function changeUsername(?User $user, string $username) : void
    {
        if($user != null) {
            $user->setUsername($username);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function changeEmail(?User $user, string $email) : void
    {
        if($user != null) {
            $user->setEmail($email);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function changePassword(?User $user, UserPasswordHasherInterface $userPasswordHasher,
                                    PasswordEditor $passwordEditor) : void
    {
        if($user != null) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $passwordEditor->getNewPassword()));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}