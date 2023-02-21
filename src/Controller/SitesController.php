<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Repository\SitesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class SitesController extends AbstractController
{
    private SitesRepository $sitesRepository;

    public function __construct(SitesRepository $sitesRepository)
    {
        $this->sitesRepository = $sitesRepository;
    }
    #[Route('/sites', name: 'app_sites')]
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
    public function delete($id): Response
    {
        $site = $this->sitesRepository->find($id);
        $this->sitesRepository->remove($site, true);
        return $this->redirectToRoute('app_sites');
    }
}
