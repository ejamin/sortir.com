<?php

namespace App\Controller;

use App\Repository\EtatsRepository;
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
    private EtatsRepository $etatsRepository;

    public function __construct(SortiesRepository $sortiesRepository, SitesRepository $sitesRepository,EtatsRepository $etatsRepository){
        $this->sortiesRepository = $sortiesRepository;
        $this->sitesRepository = $sitesRepository;
        $this->etatsRepository = $etatsRepository;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(Request $request): Response
    {
        $this->changementEtat();

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

    public function changementEtat() #Créér - Ouverte - Clôturée - Activitée en cours - Passée - Annulée - Archivée
    {
        $sorties = $this->sortiesRepository->findAll();
        $now = new \DateTime('now');
        $etatCreer = $this->etatsRepository->findOneBy(['libelle' => 'Créér']);
        $etatAnnuler = $this->etatsRepository->findOneBy(['libelle' => 'Annulée']);

        foreach ($sorties as $sortie) {
            $minutesToAdd = $sortie->getDuree();
            if(!$etatCreer or !$etatAnnuler) {
                if ($now >= $sortie->getDateDebut() && $now <= ($sortie->getDateDebut()->modify("+{$minutesToAdd} minutes"))) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Activitée en cours']));
                    $this->sortiesRepository->save($sortie, true);
                    return;
                    break;
                }
                if ($now > ($sortie->getDateDebut()->modify("+{$minutesToAdd} minutes")) && $now <= ($sortie->getDateDebut()->modify("+43800 minutes"))) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Passée']));
                    $this->sortiesRepository->save($sortie, true);
                    return;
                    break;
                }
                if ($now > $sortie->getDateFin()) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Clôturée']));
                    $this->sortiesRepository->save($sortie, true);
                    return;
                    break;
                }
            }
            if($now > ($sortie->getDateDebut()->modify("+43800 minutes"))) {
                $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Archivée']));
                $this->sortiesRepository->save($sortie, true);
                return; break;
            }
        }
    }

}
