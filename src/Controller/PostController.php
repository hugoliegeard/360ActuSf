<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/post')]
class PostController extends AbstractController
{

    #[Route('/create', name: 'post_create', methods: ['GET', 'POST'])]
    public function createPost(Request $request,
                               SluggerInterface $slugger,
                               #[Autowire('%kernel.project_dir%/public/uploads/posts')] string $postsDirectory,
                               EntityManagerInterface $entityManager): Response
    {
        # Récupération de l'utilisateur connecté dans un controller
        $user = $this->getUser();

        # Création d'un article
        $post = new Post();

        # Affectation de l'utilisateur connecté à l'article
        $post->setUser($user);

        # Création du formulaire
        $form = $this->createForm(PostType::class, $post)
            ->handleRequest($request);

        # Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # Upload de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            # Si une image est uploadée (obligatoire)
            if ($imageFile) {
                $safeFilename = $slugger->slug($post->getTitle());
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                # Déplace le fichier dans le répertoire où sont stockées les images
                try {
                    $imageFile->move($postsDirectory, $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }

            # Sauvegarde en base de données
            $entityManager->persist($post);
            $entityManager->flush();

            # Message flash
            $this->addFlash('success', 'Votre article a bien été créé');

            # Redirection
            return $this->redirectToRoute('home');

        }

        return $this->render('post/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function editPost($id): Response
    {
        return new Response('Edition de l\'article n°' . $id);
    }

    #[Route('/{id}/delete', name: 'post_delete', methods: ['GET'])]
    public function deletePost($id): Response
    {
        return new Response('Suppression de l\'article n°' . $id);
    }

    #[Route('/list', name: 'post_list', methods: ['GET'])]
    public function listPost(): Response
    {
        return new Response('Liste des articles');
    }
}
