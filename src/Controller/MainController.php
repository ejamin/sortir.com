<?php

namespace App\Controller;

use App\Repository\EtatsRepository;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use App\Services\UpdtatedServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FiltreFormType;

class MainController extends AbstractController
{   
    private SortiesRepository $sortiesRepository;
    private UpdtatedServices $services;

    public function __construct(SortiesRepository $sortiesRepository,UpdtatedServices $services){
        $this->sortiesRepository = $sortiesRepository;
        $this->services = $services;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $this->services->setEtat();
        
        $data = ['message' => 'Type your message here'];        
        $form = $this->createForm(FiltreFormType::class, $data);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sorties = $this->sortiesRepository->filter($data['sites'], $data['searchText'], $data['dateMin'], $data['dateMax'], $data['organisateur'],$data['inscrit'],$data['pasInscrit'],$data['passees'],$this->getUser()->getID());
        }else{
            $sorties = $this->sortiesRepository->findAll();
        }

        return $this->render('accueil.html.twig', [
            "now" => date('d-m-y h:i:s'),
            "form" => $form->createView(),
            "sorties" => $sorties,
            "data" => $data,
            "user" => $this->getUser()
        ]);
    }

}
