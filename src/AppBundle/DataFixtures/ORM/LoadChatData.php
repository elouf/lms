<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Chat;
use AppBundle\Entity\ChatPost;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadChatData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 3, 5);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 8, 10);
        $ress = $this->createItem($manager,
            'Chat Algèbre 1',
            'description du chat 1',
            $this->getReference('cours_alg'),
            $dateD,
            $dateF
            );
        $this->createPost($manager, $ress, 'texte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $ress, 'texte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $ress, 'texte de mon 3ème post', $this->getReference('user_admin'));
        $this->addReference('chat_alg_1', $ress);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 8, 5);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 8, 10);
        $ress = $this->createItem($manager,
            'Chat Algèbre 2',
            'description du chat 2',
            $this->getReference('cours_alg'),
            $dateD,
            $dateF
        );
        $this->addReference('chat_alg_2', $ress);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 3, 5);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 8, 10);
        $ress = $this->createItem($manager,
            'Chat Analyse 2',
            'description du chat 1',
            $this->getReference('cours_analyse'),
            $dateD,
            $dateF
        );
        $this->createPost($manager, $ress, 'texte de mon premier post', $this->getReference('user_admin'));
        $this->createPost($manager, $ress, 'texte de mon 2ème post', $this->getReference('user_etudiant_1'));
        $this->createPost($manager, $ress, 'texte de mon 3ème post', $this->getReference('user_admin'));
        $this->addReference('chat_analyse_1', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours, $dateD, $dateF){
        $item = new Chat();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $item->setDateDebut($dateD);
        $item->setDateFin($dateF);
        $manager->persist($item);
        return $item;
    }

    public function createPost(ObjectManager $manager, $chat, $texte, $auteur){
        $item = new ChatPost();
        $item->setChat($chat);
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