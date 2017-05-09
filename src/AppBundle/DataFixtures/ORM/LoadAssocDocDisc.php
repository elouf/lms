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
            $this->getReference('doc_0'),
            true
            );
        $ress = $this->createItem($manager,
            $this->getReference('disc_lettres'),
            $this->getReference('doc_1'),
            false
        );
        $ress = $this->createItem($manager,
            $this->getReference('disc_maths'),
            $this->getReference('doc_2'),
            false
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $disc, $doc, $isImportant){
        $item = new AssocDocDisc();
        $item->setDiscipline($disc);
        $item->setDocument($doc);
        $item->setIsImportant($isImportant);
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