<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UserManagementService {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }


    /**
     * Register a user with its name, email if they don't appear in database,
     *  and password if it matches confirm password.
     * @throws Exception
     */
    public function registerUser(string $userName, string $passwd, string $confirmPasswd, string $email) : void {
        if(strcmp($confirmPasswd, $passwd) != 0) {
            throw new Exception('Confirm password and initial password are not the same');
        }
        if($this->entityManager->getRepository(User::class)->findOneByUsername($userName) != null) {
            throw new Exception('This username is already used');
        }
        if($this->entityManager->getRepository(User::class)->findOneByEmail($email) != null) {
            throw new Exception('This email is already used');
        }
        $user = new User();
        $user->setName($userName);
        $user->setEmail($email);
        $user->setPasswd($passwd);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}