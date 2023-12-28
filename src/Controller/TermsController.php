<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TermsController  extends AbstractController
{
    #[Route('/terms', name: 'app_terms')]
    public function viewTerms(): Response
    {
        return $this->render('legal_rights/terms_of_use.html.twig');
    }
}