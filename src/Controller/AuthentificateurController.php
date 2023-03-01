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
use App\Service\FileUploader;
use App\Form\UploadFileFormType;
use App\Repository\SitesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AuthentificateurController extends AbstractController
{
    private ParticipantsRepository $participantsRepository;
    private SitesRepository $sitesRepository;

    public function __construct(ParticipantsRepository $participantsRepository, SitesRepository $sitesRepository)
    {
        $this->participantsRepository = $participantsRepository;
        $this->sitesRepository = $sitesRepository;
    }

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }

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

        return $this->render('participants/create.html.twig', [
            'participantForm' => $participantForm->createView(),
        ]);
    }

    #[Route('/mon-profil/update/{id}', name: 'update_participant')]
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

    #[Route('/inscription-admin', name: 'app_inscription_admin')]
    public function inscriptionMultiple(Request $request, UserPasswordHasherInterface $userPasswordHasher, FileUploader $fileUploader): Response{
        $part = new Participants();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $participantForm = $this->createForm(ParticipantsFormType::class,$part);
        $formFile = $this->createForm(UploadFileFormType::class);
        $formFile->handleRequest($request);
        $sites = $this->sitesRepository->findAll();

        if($participantForm->isSubmitted() && $participantForm->isValid()){
             #Enregistrement de l'image
             $file = $participantForm->get('image')->getData();
             /**
              * @var UploadedFile $file
              */
             if ($file) {
                 $newFileName = $part->getNom() . '-' . uniqid() . '.' . $file->guessExtension();
                 $file->move($this->getParameter('upload_image_participant'), $newFileName);
                 $part->setImage($newFileName);
             }else{
                 $part->setImage('public/image/default_profile.png');
             }
 
             $part->setPassword(
                 $userPasswordHasher->hashPassword(
                     $part,
                     $participantForm->get('password')->getData()
                 )
             );
             $this->participantsRepository->save($part,true);
             return $this->redirectToRoute("app_accueil");
        }

        if ($formFile->isSubmitted()) {           
            $participantFile = $formFile->get('participants')->getData();
            if ($participantFile) {
                $participantFileName = $fileUploader->upload($participantFile);
                $arrayDatas = [];
                $row = 0;
                if (($handle = fopen($this->getParameter("upload_directory")."/".$participantFileName, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        array_push($arrayDatas, $data[0]);
                        $row++;
                        if($row==1) continue; 
                        $participantData = explode(";",$data[0]);
                        if(!$this->participantsRepository->exists($participantData[2],$participantData[3],$participantData[0])){
                            $participant = new Participants();
                            $participant->setEmail($participantData[0]);
                            $participant->setPseudo($participantData[1]);
                            $participant->setNom($participantData[2]);
                            $participant->setPrenom($participantData[3]);  
                            $participant->setTelephone($participantData[4]);                      
                            $participant->setRoles([$participantData[5]]);
                            $participant->setIsActif(true);
                            $participant->setIsAdmin($participant->hasRoles("ROLE_ADMIN"));
                            $participant->setPassword(
                                $userPasswordHasher->hashPassword(
                                        $participant,
                                        $participantData[6]
                                    )
                                );
                            $campus = array_filter($sites, fn($s) => strcasecmp($participantData[7], $s->getNom()) == 0 );
                            if(sizeof($campus)> 0){
                                $participant->setIdSites($campus[array_key_first($campus)]);
                            }
                            $this->participantsRepository->save($participant, true);   
                        }                     
                                                
                    }
                    fclose($handle);               
                    return $this->redirectToRoute('app_accueil');
                }
            }            
        }
        return $this->render("participants/createAdmin.html.twig", 
            ["createForm" => $formFile->createView() , 
            'participantForm' => $participantForm->createView()
        ]);
    }
}
