<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssocDocDiscData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            $this->getReference('disc_maths'),
            $this->getReference('doc_0')
            );
        $ress = $this->createItem($manager,
            $this->getReference('disc_lettres'),
            $this->getReference('doc_1')
        );
        $ress = $this->createItem($manager,
            $this->getReference('disc_maths'),
            $this->getReference('doc_2')
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, Discipline $disc, Document $doc){
        $item = new AssocDocDisc();
        $item->setDiscipline($disc);
        $item->setDocument($doc);
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
        return 20;
    }
}