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

}