<?php

namespace App\Controller;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class TerminologioController extends AbstractController {

    #[Route('/', name:'app_terminologio_index')]
    public function index() : Response {
        return $this->render('index.html.twig', ['controller_name' => 'TerminologioController']);
    }

    #[Route('/inscription', name:'app_terminologio_inscription')]
    public function inscription() : Response {
        return $this->render('inscription.html.twig', []);
    }

    #[Route('/connect', name:'app_terminologio_connection')]
    public function connection() : Response {
        return $this->render('connect.html.twig', []);
    }

    #[Route('/signingIn', name:'app_terminologio_signingIn')]
    public function signingIn(string $user_name, string $user_mail, string $user_passwd, string $user_passwd_confirm) : Response {
        try {
            $userManagementService = $this->container->get('registerUser');
        } catch (NotFoundExceptionInterface $e) {
            echo "Not found";
        } catch (ContainerExceptionInterface $e) {
            echo "Container Exception";
        }
        return $this->redirect('/');
    }

    #[Route('/connecting', name:'app_terminologio_connecting')]
    public function connecting(string $user_identification, string $user_passwd) : Response {
        $userManagementService = $this->container->get('registerUser');
        return $this->redirect('index.html.twig');
    }
}