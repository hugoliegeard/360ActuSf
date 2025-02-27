<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # Création d'un utilisateur
        $user = new User();
        $user->setEmail('hugo@actu360.com')
            ->setPassword('demo')
            ->setFirstname('Hugo')
            ->setLastname('LIEGEARD')
            ->setRoles(['ROLE_ADMIN']);

        # Sauvegarde de l'utilisateur
        $manager->persist($user);

        # Création des categories
        $politique = new Category();
        $politique->setName('Politique')
            ->setSlug('politique');

        $economie = new Category();
        $economie->setName('Economie')
            ->setSlug('economie');

        $culture = new Category();
        $culture->setName('Culture')
            ->setSlug('culture');

        $sport = new Category();
        $sport->setName('Sports')
            ->setSlug('sports');

        $sante = new Category();
        $sante->setName('Santé')
            ->setSlug('sante');

        # Sauvegarde des éléments
        $manager->persist($politique);
        $manager->persist($economie);
        $manager->persist($culture);
        $manager->persist($sport);
        $manager->persist($sante);

        # Création d'un tableau de categories
        $categories = [$politique, $economie, $culture, $sport, $sante];

        # Création des articles
        for ($i = 1; $i <= 50; $i++) {
            $post = new Post();
            $post->setTitle("Titre de l'article n°$i")
                ->setSlug("titre-de-l-article-n-$i")
                ->setContent("Contenu de l'article n°$i")
                ->setImage("https://fakeimg.pl/600x400/?text=Actu360")
                ->setCategory($categories[array_rand($categories)])
                ->setUser($user);

            $manager->persist($post);
        }

        # Déclenche l'enregistrement de toutes les données
        $manager->flush();
    }
}
