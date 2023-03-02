<?php

namespace App\Services;

use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;

class UpdtatedServices
{

    private SortiesRepository $sortiesRepository;
    private EtatsRepository $etatsRepository;

    public function __construct(SortiesRepository $sortiesRepository,EtatsRepository $etatsRepository){
        $this->sortiesRepository = $sortiesRepository;
        $this->etatsRepository = $etatsRepository;
    }

    public function setEtat(): bool {
        $this->changementEtat();
        return true;
    }

    private function changementEtat() #Créér - Ouverte - Clôturée - Activitée en cours - Passée - Annulée - Archivée
    {
        $sorties = $this->sortiesRepository->findAll();
        foreach ($sorties as $sortie) {
            $nbParticipantTotal = $sortie->getNbInscritMax();
            $nbParticipant = $sortie->getIdParticipant()->count();
            $debut = $sortie->getDateDebut();
            $fin = $sortie->getDateFin();
            $duree = $sortie->getDuree();
            $now = new \DateTime;
            $archive = $sortie->getDateDebut()->modify("+43800 minutes");

            if($sortie->getIdEtat() !== $this->etatsRepository->findOneBy(['libelle' => 'Archivée'])) {
                if($now < $fin && $nbParticipant < $nbParticipantTotal) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Ouverte']));
                    $this->sortiesRepository->save($sortie, true);
                } elseif($now > $debut && $now < ($debut->modify("+{$duree} minutes"))) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Activitée en cours']));
                    $this->sortiesRepository->save($sortie, true);
                } elseif($now > $fin || $nbParticipant == $nbParticipantTotal) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Clôturée']));
                    $this->sortiesRepository->save($sortie, true);
                } elseif($now > ($debut->modify("+{$duree} minutes"))) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Passée']));
                    $this->sortiesRepository->save($sortie, true);
                } elseif($now > $archive) {
                    $sortie->setIdEtat($this->etatsRepository->findOneBy(['libelle' => 'Archivée']));
                    $this->sortiesRepository->save($sortie, true);
                }
            }
        }
    }

}