<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use App\Services\UpdtatedServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/campus')]
class SitesController extends AbstractController
{
    private SitesRepository $sitesRepository;
    private SortiesRepository $sortiesRepository;

    public function __construct(SitesRepository $sitesRepository,SortiesRepository $sortiesRepository,UpdtatedServices $services)
    {
        $this->sitesRepository = $sitesRepository;
        $this->sortiesRepository = $sortiesRepository;
        $this->services = $services;
    }
    #[Route('/', name: 'app_sites')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

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

    #[Route('/{id}', name: 'read_sites')]
    public function read($id): Response
    {
        $this->denyAccessUnlessGranted("ROLE_PARTICIPANT");

        $this->services->setEtat();

        $sorties = $this->sortiesRepository->findAll();
        $site = $this->sitesRepository->find($id);

        if (!$site) {
            throw $this->createNotFoundException("Siteinexistante");
        }



        return $this->render('sites/read.html.twig', ['site' => $site,'sorties' => $sorties]);
    }
}
