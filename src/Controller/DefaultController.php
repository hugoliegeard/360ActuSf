<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    public function home(PostRepository $postRepository): Response
    {
        # Récupération des 6 derniers articles, ordonnés par date de création décroissante
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], 6);

        # Affichage de la vue et le passage de la variable posts
        return $this->render('default/home.html.twig', [
            'posts' => $posts
        ]);
    }

    # http://localhost:8000/category/politique
    #[Route('/category/{slug}', name: 'default_category', methods: ['GET'])]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        # Récupération de la catégorie
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        return $this->render('default/category.html.twig', [
            'category' => $category
        ]);
    }

    # http://localhost:8000/politique/monsuper-article_42.html
    # cf. https://symfony.com/doc/current/routing.html#parameter-conversion
    #[Route('/{category}/{slug}_{id}.html', name: 'default_post', methods: ['GET'])]
    public function post(Post $post): Response
    {
        return $this->render('default/post.html.twig', [
            'post' => $post
        ]);
    }

    # TODO : Créer une page/route permettant de consulter les articles d'un auteur

    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
        #return new Response('Contact me!');
    }

    public function legal(): Response
    {
        return new Response('Mentions Légales!');
    }

}
