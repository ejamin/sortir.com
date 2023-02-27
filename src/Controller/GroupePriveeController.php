<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeFormType;
use App\Repository\GroupeRepository;
use App\Repository\ParticipantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/groupe')]
class GroupePriveeController extends AbstractController
{

    private GroupeRepository $groupeRepository;
    private ParticipantsRepository $participantsRepository;

    public function __construct(GroupeRepository $groupeRepository, ParticipantsRepository $participantsRepository)
    {
        $this->groupeRepository = $groupeRepository;
        $this->participantsRepository = $participantsRepository;
    }

    #[Route('/', name: 'app_groupe')]
    public function index(): Response
    {
        $groupes = $this->groupeRepository->findAll();
        $user = $this->getUser();
        return $this->render('groupe_privee/index.html.twig', ['groupes' => $groupes,'participant' => $user]);
    }

    #[Route('/création', name: 'create_groupe')]
    public function create(Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $groupe = new Groupe();
        $groupeForm = $this->createForm(GroupeFormType::class,$groupe);
        $groupeForm->handleRequest($request);

        if($groupeForm->isSubmitted() && $groupeForm->isValid()) {
            try{
                #Enregistrement de l'image
                $file = $groupeForm->get('photo')->getData();
                /**
                 * @var UploadedFile $file
                 */
                if ($file) {
                    $newFileName = $groupe->getName() . '-' . uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_image_groupe'), $newFileName);
                    $groupe->setPhoto($newFileName);
                }
                $groupe->setDateCreated(new \DateTime());
                $groupe->setNbInscrit(0);
                $this->groupeRepository->save($groupe,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }

            return $this->redirectToRoute('read_groupe',['id' => $groupe->getId()]);
        }

        return $this->render('groupe_privee/create.html.twig', ['groupeForm' => $groupeForm->createView()]);
    }

    #[Route('/{id}', name: 'read_groupe')]
    public function read($id): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        $user = $this->getUser();
        $participants = $this->participantsRepository->findAll();
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }
        $groupe = $this->groupeRepository->find($id);
        if (!$groupe) {
            throw $this->createNotFoundException("Groupe inexistant");
        }

        return $this->render('groupe_privee/read.html.twig', ['groupe' => $groupe,'participant' => $user,'participants' => $participants]);
    }

    #[Route('/modification/{id}', name: 'update_groupe')]
    public function update($id,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $groupe = $this->groupeRepository->find($id);
        if (!$groupe) {
            throw $this->createNotFoundException("Groupe inexistant");
        }
        $groupeForm = $this->createForm(GroupeFormType::class,$groupe);
        $groupeForm->handleRequest($request);

        if($groupeForm->isSubmitted() && $groupeForm->isValid()) {
            try{
                #Enregistrement de l'image
                $file = $groupeForm->get('photo')->getData();
                /**
                 * @var UploadedFile $file
                 */
                if ($file) {
                    $newFileName = $groupe->getName() . '-' . uniqid() . '.' . $file->guessExtension();
                    $file->move($this->getParameter('upload_image_groupe'), $newFileName);
                    $groupe->setPhoto($newFileName);
                }
                $groupe->setDateCreated(new \DateTime());
                $groupe->setNbInscrit(0);
                $this->groupeRepository->save($groupe,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }

            return $this->redirectToRoute('read_groupe',['id' => $groupe->getId()]);
        }

        return $this->render('groupe_privee/update.html.twig', ['groupeForm' => $groupeForm->createView(),'sortie' => $groupe]);
    }

    #[Route('/suppression/{id}', name: 'delete_groupe')]
    public function delete($id,CsrfTokenManagerInterface $csrfTokenManager,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $groupe = $this->groupeRepository->find($id);
        if($token = new CsrfToken('app_delete_groupe', $request->query->get('_csrf_token'))) {
            if(!$csrfTokenManager->isTokenValid($token)) {
                $this->addFlash('warning', '');
                throw $this->createAccessDeniedException('Jeton CSRF invalide');
            } else {
                try {
                    $this->groupeRepository->remove($groupe, true);
                } catch (\Exception $exception) {
                    $this->addFlash('error', '');
                }
            }
        }
        return $this->redirectToRoute('app_groupe');
    }


    #-- INSCRIPTION / DESISTER --#

    #[Route('/inscription/{groupeID}/{participantID}', name: 'groupe_inscription')]
    public function inscriptions($groupeID,$participantID,EntityManagerInterface $entityManager)
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $groupe = $this->groupeRepository->find($groupeID);
        $participant = $this->participantsRepository->find($participantID);

        if(!$groupe && !$participant) {
            throw $this->createNotFoundException("Non autorisé !");
        }

        $groupe->addIdParticipant($participant);
        $entityManager->persist($groupe);
        $entityManager->flush();

        return $this->redirectToRoute('app_groupe');
    }


    #[Route('/desister/{groupeID}/{participantID}', name: 'groupe_desister')]
    public function desister($groupeID,$participantID,EntityManagerInterface $entityManager)
    {
        $isParticipant = $this->isGranted("ROLE_PARTICIPANT");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux personnes inscrites sur ce site!");
        }

        $groupe = $this->groupeRepository->find($groupeID);
        $participant = $this->participantsRepository->find($participantID);

        if(!$groupe && !$participant) {
            throw $this->createNotFoundException("Non autorisé !");
        }

        $groupe->removeIdParticipant($participant);
        $entityManager->persist($groupe);
        $entityManager->flush();

        return $this->redirectToRoute('app_groupe');
    }
}
