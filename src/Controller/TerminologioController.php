<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class TerminologioController extends AbstractController {

    #[Route('/', name:'app_terminologio_index')]
    public function index() : Response {
        return $this->render('index.html.twig', []);
    }

    #[Route('/inscription', name:'app_terminologio_inscription')]
    public function inscription() : Response {
        return $this->render('inscription.html.twig', []);
    }

    #[Route('/connect', name:'app_terminologio_connection')]
    public function connection() : Response {
        return $this->render('connect.html.twig', []);
    }
}