<?php

namespace App\Controller;

use App\Form\AdminEditUserFormType;
use App\Form\FlightType;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/users', name: 'app_admin_users')]
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findBy([], ["lastnameUser" => "ASC"])
        ]);
    }

    #[Route('/users/block/{id}', name: 'app_admin_user_block')]
    public function block(Request $request, UserRepository $userRepository): Response
    {
        $param = $request->get("id");
        if ((int)$param === $this->getUser()->getId()) {
            $this->addFlash("error", "Impossible de se bloquer");
            return $this->redirectToRoute('app_admin_users');
        }
        $user = $userRepository->find($param);
        if (!$user) {
            $this->addFlash("error", "Utilisateur inconnu");
            return $this->redirectToRoute('app_admin_users');
        }

        if (!$user->isActive()) {
            $user->setActive(true);
            $this->addFlash("success", "Utilisateur débloqué");
        } elseif ($user->isActive() && !$user->isVerified()) {
            $user->setIsVerified(true);
            $this->addFlash("success", "Utilisateur activé");
        } elseif ($user->isActive()) {
            $user->setActive(false);
            $this->addFlash("success", "Utilisateur bloqué");
        }

        $userRepository->add($user, true);
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/edit/{id}', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository, CityRepository $cityRepository): Response
    {
        $param = $request->get("id");
        $user = $userRepository->find($param);
        if (!$user) {
            $this->addFlash("error", "Utilisateur inconnu");
            return $this->redirectToRoute('app_admin_users');
        }

        $cities = $cityRepository->findAll();
        $form = $this->createForm(AdminEditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/editUser.html.twig', [
            'userEdit' => $user,
            'requestForm' => $form
        ]);


//        return $this->redirectToRoute('app_admin_users');
    }
}
