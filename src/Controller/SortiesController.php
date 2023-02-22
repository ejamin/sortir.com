<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortieAnnulerFormType;
use App\Form\SortieFormType;
use App\Repository\EtatsRepository;
use App\Repository\ParticipantsRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

#[Route('/sorties')]
class SortiesController extends AbstractController
{
    private SortiesRepository $sortiesRepository;
    private EtatsRepository $etatsRepository;
    private ParticipantsRepository $participantsRepository;

    public function __construct(SortiesRepository $sortiesRepository, EtatsRepository $etatsRepository, ParticipantsRepository $participantsRepository)
    {
        $this->sortiesRepository = $sortiesRepository;
        $this->etatsRepository = $etatsRepository;
        $this->participantsRepository = $participantsRepository;
    }

    #[Route('/', name: 'app_sorties')]
    public function index(): Response
    {
        #Formulaire de recherche
        $sorties = $this->sortiesRepository->findAll();
        return $this->render('sorties/index.html.twig', ['sorties' => $sorties]);
    }

    #[Route('/création', name: 'create_sorties')]
    public function create(Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        $user =  $this->getUser();
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $sortie = new Sorties();
        $sortie->setIdOrganisateur($user);
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            try{
                #Enregistrement de l'image
                $file = $sortieForm->get('photo')->getData();
                /**
                 * @var UploadedFile $file
                 */
                if ($file) {
                    $newFileName = $sortie->getNom() . '-' . uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_image_sortie'), $newFileName);
                    $sortie->setPhoto($newFileName);
                }
                # Logique pour différencier les deux boutons enregistrer et publier
                if($sortieForm->get('enregistrer')->isClicked()){
                    $sortie->setIdEtat($this->etatsRepository->find(1)); #Créér
                }else{
                    $sortie->setIdEtat($this->etatsRepository->find(2)); #Ouverte
                }
                $this->sortiesRepository->save($sortie,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }

            return $this->redirectToRoute('read_sorties',['id' => $sortie->getId()]);
        }

        return $this->render('sorties/create.html.twig',['sortieForm' => $sortieForm->createView()]);
    }

    #[Route('/{id}', name: 'read_sorties')]
    public function read($id): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $sortie = $this->sortiesRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Sortie inexistante");
        }
        return $this->render('sorties/read.html.twig', ['sortie' => $sortie]);
    }

    #[Route('/modification/{id}', name: 'update_sorties')]
    public function update($id,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $sortie = $this->sortiesRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Sortie inexistante");
        }
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            try{
                #Logique pour différencier les deux boutons enregistrer et publier
                if($sortieForm->get('enregistrer')->isClicked()){
                    $sortie->setIdEtat($this->etatsRepository->find(1)); #Créér
                }else{
                    $sortie->setIdEtat($this->etatsRepository->find(2)); #Ouverte
                }
                $this->sortiesRepository->save($sortie,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }

        return $this->render('sorties/update.html.twig',['sortieForm' => $sortieForm->createView()]);
    }

    #[Route('/annulation/{id}', name: 'delete_sorties')]
    public function delete($id,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        $isAdmin = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant || !$isAdmin) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site ou à un administrateur!");
        }

        $sortie = $this->sortiesRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException("Sortie inexistante");
        }
        $annulerForm = $this->createForm(SortieAnnulerFormType::class, $sortie);
        $annulerForm->handleRequest($request);

        if ($annulerForm->isSubmitted() && $annulerForm->isValid()) {
            try{
                $sortie->setIdEtat($this->etatsRepository->find(4));
                $this->sortiesRepository->save($sortie,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        return $this->render('sorties/delete.html.twig', ['annulerForm' => $annulerForm->createView()]);
    }

    #-- INSCRIPTION / DESISTER --#

    #[Route('/inscription', name: 'inscription_sorties')]
    public function inscription(Request $request)
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        #Récupération de l'ID de la sortie et du participant lors du click
        $idSortie = (int)$request->query->get('idSortie');
        $idParticipant = (int)$request->query->get('idParticipant');
        $sortie = $this->sortiesRepository->find($idSortie);
        $participant = $this->participantsRepository->find($idParticipant);

        #Ajout en base de donnée
        $sortie->addIdParticipant($participant);
        $this->sortiesRepository->save($sortie);

        return $this->render('appInscritDesister/ajax.html.twig',['nbInscrit' => $sortie->getIdParticipant()->count(),'sortie' => $sortie]);
    }

    #[Route('/desister', name: 'desister_sorties')]
    public function desister(Request $request)
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        #Récupération de l'ID de la sortie et du participant lors du click
        $idSortie = (int)$request->query->get('idSortie');
        $idParticipant = (int)$request->query->get('idParticipant');
        $sortie = $this->sortiesRepository->find($idSortie);
        $participant = $this->participantsRepository->find($idParticipant);

        #Suppression en base de donnée
        $sortie->removeIdParticipant($participant);
        $this->sortiesRepository->save($sortie);

        return $this->render('appInscritDesister/ajax.html.twig',['nbInscrit' => $sortie->getIdParticipant()->count(),'sortie' => $sortie]);

    }

}


