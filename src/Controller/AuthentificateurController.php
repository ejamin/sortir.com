<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\ParticipantsFormType;
use App\Repository\ParticipantsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthentificateurController extends AbstractController
{
    private ParticipantsRepository $participantsRepository;

    public function __construct(ParticipantsRepository $participantsRepository)
    {
        $this->participantsRepository = $participantsRepository;
    }

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/déconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/inscription', name: 'create_participant')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {
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
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('participants/create.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }

    #[Route('/mon-profil/{id}', name: 'update_participant')]
    public function update(Request $request, UserPasswordHasherInterface $userPasswordHasher, $id): Response {
        $participant = $this->participantsRepository->find($id);
        $user = $this->getUser();

        if ($participant !== $user){
            return $this->redirectToRoute("app_403");
        }

        $participantForm = $this->createForm(ParticipantsFormType::class,$participant);

        $participantForm->handleRequest($request);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            #Enregistrement de l'image
            $file = $participantForm->get('image')->getData();
            /**
             * @var UploadedFile $file
             */
            if ($file) {
                $newFileName = $participant->getNom() . '-' . uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_image_participant'), $newFileName);
                $participant->setImage($newFileName);
            }else{
                $participant->setImage('public/image/default_profile.png');
            }

            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $participantForm->get('password')->getData()
                )
            );
            $this->participantsRepository->save($participant,true);
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('participants/update.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }
}
