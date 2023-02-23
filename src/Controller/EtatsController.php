<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Repository\EtatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class EtatsController extends AbstractController
{
    private EtatsRepository $etatsRepository;

    public function __construct(EtatsRepository $etatsRepository)
    {
        $this->etatsRepository = $etatsRepository;
    }

    #[Route('/etats', name: 'app_etats')]
    public function index(): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        #Création des états à l'arriver de la page
        if(!$this->etatsRepository->findBy(['libelle' => 'Créer'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Créér');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Ouverte'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Ouverte');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Clôturée'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Clôturée');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Activitée en cours'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Activitée en cours');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Passée'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Passée');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Annulée'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Annulée');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->etatsRepository->findBy(['libelle' => 'Archivée'])) {
            $etat = new Etats();
            try{
                $etat->setLibelle('Archivée');
                $this->etatsRepository->save($etat,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }

        $etats = $this->etatsRepository->findAll();

        return $this->render('etats/index.html.twig', ['etats' => $etats]);
    }

    #[Route('/suppression/{id}', name: 'delete_etats')]
    public function delete($id,CsrfTokenManagerInterface $csrfTokenManager,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $etat = $this->etatsRepository->find($id);
        if($token = new CsrfToken('app_delete_etat', $request->query->get('_csrf_token'))) {
            if(!$csrfTokenManager->isTokenValid($token)) {
                $this->addFlash('warning', '');
                throw $this->createAccessDeniedException('Jeton CSRF invalide');
            } else {
                try {
                    $this->etatsRepository->remove($etat, true);
                } catch (\Exception $exception) {
                    $this->addFlash('error', '');
                }
            }
        }
        return $this->redirectToRoute('app_etats');
    }
}
