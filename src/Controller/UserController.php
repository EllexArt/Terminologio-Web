<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController  extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        return $this->render('profile/profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function deleteAccount(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if(in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('warning', "Impossible to delete admin account");
            return $this->redirectToRoute('app_profile');
        }
        $this->container->get('security.token_storage')->setToken(null);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_logout');
    }
}