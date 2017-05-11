<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Forum;
use AppBundle\Entity\ForumPost;
use AppBundle\Entity\ForumSujet;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadForumData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'Forum Algèbre 1',
            'description du forum 1',
            $this->getReference('cours_alg')
            );
        $sujet = $this->createSujet($manager, $ress, 'Sujet 1', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'texte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'texte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'texte de mon 3ème post', $this->getReference('user_admin'));
        $sujet = $this->createSujet($manager, $ress, 'zzSujet 2', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'zztexte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'zztexte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'zztexte de mon 3ème post', $this->getReference('user_admin'));
        $this->addReference('forum_alg_1', $ress);


        $ress = $this->createItem($manager,
            'Forum Algèbre 2',
            'description du forum 2',
            $this->getReference('cours_alg')
        );
        $sujet = $this->createSujet($manager, $ress, 'aaSujet 1', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'aatexte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'aatexte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'aatexte de mon 3ème post', $this->getReference('user_admin'));
        $sujet = $this->createSujet($manager, $ress, 'bbSujet 2', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'bbtexte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'bbtexte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'bbtexte de mon 3ème post', $this->getReference('user_admin'));
        $this->addReference('forum_alg_2', $ress);

        $ress = $this->createItem($manager,
            'Forum Analyse 1',
            'description du forum 1',
            $this->getReference('cours_analyse')
        );
        $sujet = $this->createSujet($manager, $ress, 'Sujet 1', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'texte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'texte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'texte de mon 3ème post', $this->getReference('user_admin'));
        $sujet = $this->createSujet($manager, $ress, 'zzSujet 2', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'zztexte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $sujet, 'zztexte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $sujet, 'zztexte de mon 3ème post', $this->getReference('user_admin'));
        $this->addReference('forum_analyse_1', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours){
        $item = new Forum();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $manager->persist($item);
        return $item;
    }

    public function createSujet(ObjectManager $manager, $forum, $titre, $createur){
        $item = new ForumSujet();
        $item->setForum($forum);
        $item->setTitre($titre);
        $item->setCreateur($createur);
        $manager->persist($item);
        return $item;
    }

    public function createPost(ObjectManager $manager, $sujet, $texte, $auteur){
        $item = new ForumPost();
        $item->setSujet($sujet);
        $item->setTexte($texte);
        $item->setAuteur($auteur);
        $manager->persist($item);
        return $item;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 12;
    }
}