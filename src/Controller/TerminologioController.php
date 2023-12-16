<?php

namespace App\Controller;
use App\Entity\DTO\InscriptionFormDTO;
use App\Entity\User;
use App\Form\InscriptionType;
use App\Service\UserManagementService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
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

    #Route gérant l'inscription d'un utilisateur dans la base de données
    # et l'affichage du formulaire d'inscription
    #[Route('/inscription', name:'app_terminologio_inscription')]
    public function inscription(Request $request, UserManagementService $userManagementService) : Response {
        $inscriptionForm = new InscriptionFormDTO();
        $form = $this->createForm(InscriptionType::class, $inscriptionForm);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $inscriptionForm = $form->getData();
            try {
                $userManagementService->registerUser($inscriptionForm->getUserName(), $inscriptionForm->getUserPasswd(), $inscriptionForm->getUserPasswdConfirm(), $inscriptionForm->getUserMail());
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return $this->redirectToRoute('app_terminologio_inscription');
            }
            return $this->redirectToRoute('app_terminologio_index');
        }
        return $this->render('inscription.html.twig', ['form' => $form]);
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
        return $this->redirectToRoute('app_terminologio_index');
    }

    #[Route('/connecting', name:'app_terminologio_connecting')]
    public function connecting(string $user_identification, string $user_passwd) : Response {
        $userManagementService = $this->container->get('registerUser');
        return $this->redirectToRoute('app_terminologio_index');
    }
}