<?php

namespace App\Controller;

use App\Form\ParticipantsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Participants;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ParticipantsRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/participant',name: 'app_participant_')]
class ParticipantController extends AbstractController
{
    private ParticipantsRepository $participantsRepository;

    public function __construct(ParticipantsRepository $participantsRepository)
    {
        $this->participantsRepository = $participantsRepository;
    }

    #[Route('/', name: 'liste')]
    public function index(): Response
    {
        return $this->render('participants/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/add', name: 'create')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {
        $participant = new Participants();
        $participant->setIsActif(true);
        $participant->setIsAdmin(false);
        $participant->setRoles(["ROLE_PARTICIPANT"]);

        $participantForm = $this->createForm(ParticipantsFormType::class,$participant);
        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $participantForm->get('password')->getData()
                )
            );
            $this->participantsRepository->save($participant,true);
            return $this->redirectToRoute("app_participant_liste");
        }

        return $this->render('participants/create.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, UserPasswordHasherInterface $userPasswordHasher, $id): Response {
        $participant = $this->participantsRepository->find($id);
        $user = $this->getUser();

        if ($participant !== $user){
            return $this->redirectToRoute("app_403");
        }

        $participantForm = $this->createForm(ParticipantsFormType::class,$participant);

        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $participantForm->get('password')->getData()
                )
            );
            $this->participantsRepository->save($participant,true);
            return $this->redirectToRoute("app_participant_liste");
        }

        return $this->render('participants/update.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show($id): Response {
        $participant = $this->participantsRepository->find($id);
       
        return $this->render('participants/show.html.twig', [
            'participant' => $participant
        ]);
    }
}
