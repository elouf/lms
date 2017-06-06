<?php

namespace AppBundle\DataFixtures\FromChamilo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Discipline;
use mysqli;

class LoadDisciplineData extends LoadChamiloConnect implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $queryDisc = "SELECT * FROM course_category ORDER by ID";
        if ($resultDisc = $this->getMysqli()->query($queryDisc)) {
            while ($disc = $resultDisc->fetch_object()) {
                $oneDisc = $this->createItem($manager,
                    explode('_', $disc->name)[0],
                    'La discipline '.explode('_', $disc->name)[0],
                    'images/'.explode('_', $disc->name)[1].'.png');
                $this->addReference('disc_'.$disc->id, $oneDisc);
            }

            $resultDisc->close();
        }

        $this->getMysqli()->close();
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $imgFilePath){
        $item = new Discipline();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setImgFilePath($imgFilePath);
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
        return 1;
    }
}