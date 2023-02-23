<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/campus')]
class SitesController extends AbstractController
{
    private SitesRepository $sitesRepository;
    private SortiesRepository $sortiesRepository;

    public function __construct(SitesRepository $sitesRepository,SortiesRepository $sortiesRepository)
    {
        $this->sitesRepository = $sitesRepository;
        $this->sortiesRepository = $sortiesRepository;
    }
    #[Route('/', name: 'app_sites')]
    public function index(): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        #Création des états à l'arriver de la page
        if(!$this->sitesRepository->findBy(['nom' => 'ENI Rennes'])) {
            $site = new Sites();
            try{
                $site->setNom('ENI Rennes');
                $this->sitesRepository->save($site,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->sitesRepository->findBy(['nom' => 'ENI Nantes'])) {
            $site = new Sites();
            try{
                $site->setNom('ENI Nantes');
                $this->sitesRepository->save($site,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->sitesRepository->findBy(['nom' => 'ENI Quimper'])) {
            $site = new Sites();
            try{
                $site->setNom('ENI Quimper');
                $this->sitesRepository->save($site,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        if(!$this->sitesRepository->findBy(['nom' => 'ENI Niort'])) {
            $site = new Sites();
            try{
                $site->setNom('ENI Niort');
                $this->sitesRepository->save($site,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }

        $sites = $this->sitesRepository->findAll();

        return $this->render('sites/index.html.twig', ['sites' => $sites]);
    }

    #[Route('/suppression/{id}', name: 'delete_sites')]
    public function delete($id,CsrfTokenManagerInterface $csrfTokenManager,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $site = $this->sitesRepository->find($id);
        if($token = new CsrfToken('app_delete_sites', $request->query->get('_csrf_token'))) {
            if(!$csrfTokenManager->isTokenValid($token)) {
                $this->addFlash('warning', '');
                throw $this->createAccessDeniedException('Jeton CSRF invalide');
            } else {
                try {
                    $this->sitesRepository->remove($site, true);
                } catch (\Exception $exception) {
                    $this->addFlash('error', '');
                }
            }
        }

        return $this->redirectToRoute('app_sites');
    }

    #[Route('/{id}', name: 'read_sites')]
    public function read($id): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $sorties = $this->sortiesRepository->findAll();
        $site = $this->sitesRepository->find($id);

        if (!$site) {
            throw $this->createNotFoundException("Siteinexistante");
        }



        return $this->render('sites/read.html.twig', ['site' => $site,'sorties' => $sorties]);
    }
}
