<?php

namespace App\Controller;
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
        $userManagementService = $this->container->get('registerUser');
        return $this->redirect('index.html.twig');
    }
}