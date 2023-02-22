<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FiltreFormType;

class MainController extends AbstractController
{   
    private SortiesRepository $sortiesRepository;
    private SitesRepository $sitesRepository;

    public function __construct(SortiesRepository $sortiesRepository, SitesRepository $sitesRepository){
        $this->sortiesRepository = $sortiesRepository;
        $this->sitesRepository = $sitesRepository;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(Request $request): Response
    {
        $sorties = $this->sortiesRepository->findAll();
        $sites = $this->sitesRepository->findAll();      

        $defaultData = ['message' => 'Type your message here'];        
        $form = $this->createForm(FiltreFormType::class, $defaultData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $this->filtrerSorties($data,$sorties);

            return $this->render('accueil.html.twig', [
                "now" => date('d-m-y h:i:s'),
                "form" => $form->createView(),
                "sorties" => $sorties,
                "sites" => $sites,
                "data" => $data,
                "user" => $this->getUser()
            ]);
        }

        return $this->render('accueil.html.twig', [
            "now" => date('d-m-y h:i:s'),
            "form" => $form->createView(),
            "sorties" => $sorties,
            "sites" => $sites,
            "data" => $defaultData,
            "user" => $this->getUser()
        ]);
    }

    public function filtrerSorties($data, &$sorties){
        $sortiesFinal = [];
        foreach($sorties as $sortie){
            $valide = true;
            if(isset($data['sites'])){
                if($sortie->getIdSite()->getID() === $data['sites']->getId()){
                    $valide = true;
                }else $valide = false;
            }
            if ($valide && isset($data['searchText'])) {
                if (str_contains($sortie->getNom(), $data['searchText'])) {
                    $valide = true;
                }else $valide = false;
            }
            if($valide && isset($data['dateMin']) && isset($data['dateMax'])){
                if ($sortie->getDateDebut() >= $data['dateMin'] && $sortie->getDateDebut() < $data['dateMax']) {
                    $valide = true;
                }else $valide = false;
            }
            if($valide && isset($data['organisateur'])){
                if($data['organisateur'] == true){
                    if($this->getUser()->getID() === $sortie->getIdOrganisateur()->getID()){
                        $valide = true;                    
                    }else $valide = false;
                }
            }
            if($valide && isset($data['inscrit'])){
                if($data['inscrit'] == true){
                    if($this->existsInArray($this->getUser(), $sortie->getIdParticipant())){
                        $valide = true;                    
                    }else $valide = false;
                }
            }
            if($valide && isset($data['pasInscrit'])){
                if($data['pasInscrit'] == true){
                    if(!$this->existsInArray($this->getUser(), $sortie->getIdParticipant())){
                        $valide = true;                    
                    }else $valide = false;
                }
            }
            if($valide && isset($data['passees'])){
                if($data['passees'] == true){
                    if($sortie->getDateDebut() < date('d-m-y h:i:s')){
                        $valide = true;
                    }else $valide = false;
                }
            }

            if($valide == true){
                array_push($sortiesFinal,$sortie);                
            }
        }
        $sorties = $sortiesFinal;
    }

    public function existsInArray($entry, $array) {
        foreach ($array as $compare) {
            if ($compare->id == $entry->id) {
                return true;
            }
        }
        return false;
    }
}
