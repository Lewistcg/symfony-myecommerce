<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/edit_password', name: 'app_account_edit_password')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'ancien mot de passe
            $old_pwd = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user, $old_pwd)) {
                // Ancien mot de passe correct, récupérer le nouveau mot de passe
                $new_pwd = $form->get('new_password')->getData();

                // Hasher le nouveau mot de passe
                $hashedPassword = $encoder->hashPassword($user, $new_pwd);
                $user->setPassword($hashedPassword);

                // Mise à jour dans la base de données
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Notification de succès
                $notification = "Votre mot de passe a bien été mis à jour.";
            } else {
                // Notification d'erreur si ancien mot de passe invalide
                $notification = "L'ancien mot de passe n'est pas valide.";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
