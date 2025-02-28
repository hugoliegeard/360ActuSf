<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request,
                             UserPasswordHasherInterface $hasher,
                             EntityManagerInterface $entityManager): Response
    {
        # Créer un utilisateur vide
        $user = new User();

        # Créer un formulaire permettant de saisir un utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        # Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # Hashage du mot de passe
            $user->setPassword(
                $hasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            # Sauvegarde en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            # Message flash
            $this->addFlash('success', 'Votre compte a bien été créé');

            # Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');

        }

        # Passage du formulaire à la vue
        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);
    }
}
