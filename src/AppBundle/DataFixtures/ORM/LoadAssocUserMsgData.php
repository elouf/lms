<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssocUserMsg;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\Message;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssocUserMsgData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 7);
        $ress = $this->createItem($manager,
            $this->getReference('user_admin'),
            $this->getReference('msg_1'),
            $dateC
        );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 6);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_1'),
            $this->getReference('msg_1'),
            $dateC
            );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 4, 6);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_2'),
            $this->getReference('msg_1'),
            $dateC
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_3'),
            $this->getReference('msg_1'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_4'),
            $this->getReference('msg_1'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_5'),
            $this->getReference('msg_1'),
            null
        );

        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 12);
        $ress = $this->createItem($manager,
            $this->getReference('user_admin'),
            $this->getReference('msg_2'),
            $dateC
        );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 30);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_1'),
            $this->getReference('msg_2'),
            $dateC
        );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 4, 10);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_2'),
            $this->getReference('msg_2'),
            $dateC
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_3'),
            $this->getReference('msg_2'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_4'),
            $this->getReference('msg_2'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_5'),
            $this->getReference('msg_2'),
            null
        );

        $ress = $this->createItem($manager,
            $this->getReference('user_admin'),
            $this->getReference('msg_3'),
            null
        );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 3, 28);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_1'),
            $this->getReference('msg_3'),
            $dateC
        );
        $dateC = new \DateTime();
        $dateC->setDate(2017, 4, 20);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_2'),
            $this->getReference('msg_3'),
            $dateC
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_3'),
            $this->getReference('msg_3'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_4'),
            $this->getReference('msg_3'),
            null
        );
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_5'),
            $this->getReference('msg_3'),
            null
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $user, $message, $dateLecture){
        $item = new AssocUserMsg();
        $item->setUser($user);
        $item->setMessage($message);
        $item->setDateLecture($dateLecture);
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
        return 36;
    }
}