<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{   
    private SortiesRepository $sortiesRepository;
    private SitesRepository $sitesRepository;

    public function __construct(SortiesRepository $sortiesRepository, SitesRepository $sitesRepository){
        $this->sortiesRepository = $sortiesRepository;
        $this->sitesRepository = $sitesRepository;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        $sorties = $this->sortiesRepository->findAll();
        $sites = $this->sitesRepository->findAll();

        return $this->render('accueil.html.twig', [
            "now" => date('d-m-y h:i:s'),
            "sorties" => $sorties,
            "sites" => $sites
        ]);
    }
}
