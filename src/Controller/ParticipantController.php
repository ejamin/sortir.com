<?php

namespace App\Controller;

use App\Repository\ParticipantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    private ParticipantsRepository $participantsRepository;

    public function __construct(ParticipantsRepository $participantsRepository)
    {
        $this->participantsRepository = $participantsRepository;
    }

    #[Route('/', name: 'app_participant')]
    public function index(): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $participants = $this->participantsRepository->findAll();

        return $this->render('participants/index.html.twig', ['participants' => $participants]);
    }

    #[Route('/{id}', name: 'show')]
    public function show($id): Response {
        $participant = $this->participantsRepository->find($id);

        return $this->render('participants/show.html.twig', ['participant' => $participant]);
    }
}
