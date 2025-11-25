<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\InscriptionType;

final class InscriptionController extends AbstractController
{
    private $entityManager;

    // Injection de dépendance de l'EntityManager dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();  // créer un nouveau User

        // créer un formulaire basé sur User
        $form = $this->createForm(InscriptionType::class, $user);

        // Le formulaire "écoute" la requête HTTP
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe avant sauvegarde
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Stockage en base de données via l'attribut $entityManager
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Message flash facultatif
            $this->addFlash('success', 'Inscription réussie !');

            // Redirection après inscription réussie
            return $this->redirectToRoute('app_home'); // adapter selon la route souhaitée
        }

        // Affichage du formulaire
        return $this->render('inscription/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
