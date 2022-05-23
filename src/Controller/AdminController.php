<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Form\AdminEditUserFormType;
use App\Repository\CityRepository;
use App\Repository\SettingRepository;
use App\Repository\StatusRepository;
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
            $citiSelect = $cityRepository->find($form->get("idCity")->getData());
            $user->setCity($citiSelect);
            $userRepository->add($user, true);
            $this->addFlash("success", "Utilisateur modifié");
            return $this->redirectToRoute('app_admin_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/editUser.html.twig', [
            'userEdit' => $user,
            'requestForm' => $form, 'city' => $user->getCity()
        ]);
    }

    #[Route('/settings', name: 'app_admin_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, SettingRepository $settingRepository, StatusRepository $statusRepository): Response
    {
        $datas = $request->request->all();
        foreach ($datas as $key => $data) {
            $setting = $settingRepository->findOneBy(["keySetting" => $key]);
            $setting->setValue($data);
            $settingRepository->add($setting, true);
        }

        if ($request->getMethod() === "POST") {
            $this->addFlash("success", "Les paramètres ont été enregistrées avec succès");
        }

        $smtp = $settingRepository->findBykeyObj("SMTP");
        $status = $settingRepository->findBykeyObj("status");
        $statusList = $statusRepository->findAll();
        return $this->render('admin/settings.html.twig', ['smtp' => $smtp, "choicestatus" => $status, "statuslist" => $statusList]);
    }

    #[Route('users/delete/{idUser}', name: 'app_admin_user_delete')]
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->get("idUser"));
        if (!$user) {
            $this->addFlash("error", "Cette utilisateur n'existe pas");
            return $this->redirectToRoute("app_admin_users");
        }
        if ($user->getCompanies()->count()) {
            $this->addFlash("error", "Impossible de supprimer cet utilisateur, il a déjà créé des entreprises sur son compte");
            return $this->redirectToRoute("app_admin_users");
        }
        $userRepository->remove($user, true);
        $this->addFlash("success", "Utilisateur supprimé avec succès");
        return $this->redirectToRoute("app_admin_users");
    }

    #[Route('users/admin/{idUser}', name: 'app_admin_user_setadmin')]
    public function setAdmin(Request $request, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->get("idUser"));
        if (!$user) {
            $this->addFlash("error", "Cette utilisateur n'existe pas");
            return $this->redirectToRoute("app_admin_users");
        }
        if ($user === $this->getUser()) {
            $this->addFlash("error", "Vous ne pouvez pas vous retirer vos droits admin");
            return $this->redirectToRoute("app_admin_users");
        }
        $role = $user->getRoles();
        switch ($role[0]) {
            case "ROLE_CANDIDATE":
                $user->setRoles(["ROLE_ADMIN"]);
                break;
            default:
                $user->setRoles(["ROLE_CANDIDATE"]);
                break;
        }
        $userRepository->add($user, true);

        $this->addFlash("success", "Les droits de l'utilisateur ont été modifiés avec succès");
        return $this->redirectToRoute("app_admin_users");
    }
}
