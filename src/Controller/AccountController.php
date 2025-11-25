<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // utiliser Annotation pour la route

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')] // changement de l’URL /account → /compte
    public function index(): Response
    {
        // Plus besoin de passer 'controller_name' au template
        return $this->render('account/index.html.twig');
    }
}
