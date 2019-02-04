<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Document;
use AppBundle\Entity\Inscription;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssocDocInscrData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            $this->getReference('inscr_01'),
            $this->getReference('doc_6'),
            null,
            false
            );
        $ress = $this->createItem($manager,
            $this->getReference('inscr_01'),
            $this->getReference('doc_7'),
            null,
            true
        );
        $ress = $this->createItem($manager,
            $this->getReference('inscr_02'),
            $this->getReference('doc_8'),
            null,
            false
        );
        $ress = $this->createItem($manager,
            $this->getReference('inscr_03'),
            $this->getReference('doc_9'),
            $this->getReference('cours_alg'),
            false
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $inscr, $doc, $cours, $isImportant){
        $item = new AssocDocInscr();
        $item->setInscription($inscr);
        $item->setDocument($doc);
        $item->setIsImportant($isImportant);
        if($cours)
            $item->setCours($cours);
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