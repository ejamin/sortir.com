<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/securite')]
class SecurityController extends AbstractController
{
    #[Route('/403', name: 'app_403')]
    public function error403(): Response
    {
        return $this->render('security/403.html.twig');
    }

    #[Route('/404', name: 'app_404')]
    public function error404(): Response
    {
        return $this->render('security/404.html.twig');
    }
}
