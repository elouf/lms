<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssocDocCours;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Document;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssocDocCoursData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            $this->getReference('cours_analyse'),
            $this->getReference('doc_3')
            );
        $ress = $this->createItem($manager,
            $this->getReference('cours_alg'),
            $this->getReference('doc_4')
        );
        $ress = $this->createItem($manager,
            $this->getReference('cours_alg'),
            $this->getReference('doc_5')
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, Cours $cours, Document $doc){
        $item = new AssocDocCours();
        $item->setCours($cours);
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