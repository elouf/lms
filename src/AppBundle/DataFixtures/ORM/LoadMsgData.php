<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Devoir;
use AppBundle\Entity\Message;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMsgData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 5);
        $ress = $this->createItem($manager,
            'objet de mon message',
            'Contenu de mon message',
            $this->getReference('user_admin'),
            $dateC
            );
        $this->addReference('msg_1', $ress);

        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 9);
        $ress = $this->createItem($manager,
            'objet de mon message1',
            'Contenu de mon message1',
            $this->getReference('user_etudiant_1'),
            $dateC
        );
        $this->addReference('msg_2', $ress);

        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 10);
        $ress = $this->createItem($manager,
            'objet de mon message2',
            'Contenu de mon message2',
            $this->getReference('user_etudiant_2'),
            $dateC
        );
        $this->addReference('msg_3', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $objet, $contenu, $expediteur, $dateCrea){
        $item = new Message();
        $item->setObjet($objet);
        $item->setContenu($contenu);
        $item->setExpediteur($expediteur);
        $item->setDateCreation($dateCrea);
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
        return 35;
    }
}