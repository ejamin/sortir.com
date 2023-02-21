<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxFormType;
use App\Repository\LieuxRepository;
use App\Repository\VillesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
//        $isParticipant = $this->isGranted("ROLE_ADMIN");
//        if (!$isParticipant) {
//            throw new AccessDeniedException("Réservé aux administrateurs !");
//        }

        $lieux = $this->lieuxRepository->findAll();
        $ville =$this->villesRepository->findAll();

        $lieu = new Lieux();
        $lieuForm = $this->createForm(LieuxFormType::class,$lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            try {
                $this->lieuxRepository->save($lieu,true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }

        return $this->render('lieux/index.html.twig', ['lieux' => $lieux, 'villes' => $ville,'lieuForm' => $lieuForm->createView()]);
    }

    #[Route('/modification/{id}', name: 'update_lieux')]
    public function update($id,Request $request): Response
    {
        $lieu = $this->lieuxRepository->find($id);
        $lieuForm = $this->createForm(LieuxFormType::class,$lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            try {
                $this->lieuxRepository->save($lieu, true);
                $this->addFlash('success', '');
            } catch (\Exception $exception) {
                $this->addFlash('error', '');
            }
        }
        return $this->render('lieux/index.html.twig', []);
    }

    #[Route('/suppression/{id}', name: 'delete_lieux')]
    public function delete($id): Response
    {
        $lieu = $this->lieuxRepository->find($id);
        $this->lieuxRepository->remove($lieu, true);
        return $this->redirectToRoute('app_lieux');
    }
}
