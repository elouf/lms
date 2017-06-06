<?php

namespace AppBundle\DataFixtures\FromChamilo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Institut;
use mysqli;

class LoadInstitutData extends LoadChamiloConnect implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $queryInst = "SELECT * FROM _stdi_instituts ORDER by ID";
        if ($resultInst = $this->getMysqli()->query($queryInst)) {
            while ($inst = $resultInst->fetch_object()) {
                $oneInst = $this->createItem($manager,
                    $inst->nom,
                    '');
                $this->addReference('inst_'.$inst->id, $oneInst);
            }

            $resultInst->close();
        }

        $this->getMysqli()->close();
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $ville){
        $item = new Institut();
        $item->setNom($nom);
        $item->setVille($ville);
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
        return 3;
    }
}