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
        //$this->changementEtat();


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
