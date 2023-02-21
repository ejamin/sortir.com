<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Entity\Villes;
use App\Form\LieuxFormType;
use App\Form\VilleFormType;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/lieux')]
class LieuxController extends AbstractController
{

    private LieuxRepository $lieuxRepository;
    private VillesRepository $villesRepository;

    public function __construct(LieuxRepository $lieuxRepository, VillesRepository $villesRepository)
    {
        $this->lieuxRepository = $lieuxRepository;
        $this->villesRepository = $villesRepository;
    }

    #[Route('/', name: 'app_lieux')]
    public function index(Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $lieu = new Lieux();
        $lieuForm = $this->createForm(LieuxFormType::class,$lieu);
        $lieuForm->handleRequest($request);

        $ville = new Villes();
        $villeForm = $this->createForm(VilleFormType::class,$ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()) {
            $this->villesRepository->save($ville,true);
            try {
                $lieu->setIdVille($ville);
                $this->lieuxRepository->save($lieu,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
            return $this->redirectToRoute('app_lieux');
        }

        $lieux = $this->lieuxRepository->findAll();
        $villes =$this->villesRepository->findAll();

        return $this->render('lieux/index.html.twig', ['lieux' => $lieux, 'villes' => $villes,
            'lieuForm' => $lieuForm->createView(),'villeForm' => $villeForm->createView()]);
    }

    #[Route('/modification/{id}', name: 'update_lieux')]
    public function update($id,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $lieu = $this->lieuxRepository->find($id);
        $lieuForm = $this->createForm(LieuxFormType::class,$lieu);
        $lieuForm->handleRequest($request);

        $ville = $this->villesRepository->find($id);
        $villeForm = $this->createForm(VilleFormType::class,$ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()) {
            $this->villesRepository->save($ville,true);
            try {
                $lieu->setIdVille($ville);
                $this->lieuxRepository->save($lieu,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
            return $this->redirectToRoute('app_lieux');
        }

        return $this->render('lieux/update.html.twig', ['lieuForm' => $lieuForm->createView(),'villeForm' => $villeForm->createView()]);
    }

    #[Route('/suppression/{id}', name: 'delete_lieux')]
    public function delete($id,CsrfTokenManagerInterface $csrfTokenManager,Request $request): Response
    {
        $isParticipant = $this->isGranted("ROLE_ADMIN");
        if (!$isParticipant) {
            throw new AccessDeniedException("Réservé aux administrateurs !");
        }

        $lieu = $this->lieuxRepository->find($id);
        if($token = new CsrfToken('app_delete_lieux', $request->query->get('_csrf_token'))) {
            if(!$csrfTokenManager->isTokenValid($token)) {
                $this->addFlash('warning', '');
                throw $this->createAccessDeniedException('Jeton CSRF invalide');
            } else {
                try {
//                    $ville = $lieu->getIdVille();
//                    $this->villesRepository->remove($ville,true);
                    $this->lieuxRepository->remove($lieu, true);
                } catch (\Exception $exception) {
                    $this->addFlash('error', '');
                }
            }
        }
        return $this->redirectToRoute('app_lieux');
    }
}


