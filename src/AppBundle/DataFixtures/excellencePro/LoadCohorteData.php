<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cohorte;

class LoadCohorteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $coh = $this->createItem($manager,
            'ExcellencePro',
            [$this->getReference('disc_excellencePro')],
            []);
        $this->addReference('coh_excellencePro', $coh);
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $disciplines, $courses){
        $item = new Cohorte();
        $item->setNom($nom);
        foreach($disciplines as $dis){
            $item->addDiscipline($dis);
        }
        foreach($courses as $cours){
            $item->addCours($cours);
        }
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