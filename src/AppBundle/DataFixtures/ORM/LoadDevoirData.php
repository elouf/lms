<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Devoir;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDevoirData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 1, 1);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 1, 10);
        $ress = $this->createItem($manager,
            'Devoir d\'Algèbre 2017 n°1',
            'Devoir d\'algèbre linéaire portant sur la théorie des anneaux',
            $this->getReference('cours_alg'),
            $dateD,
            $dateF,
            17200
            );
        $this->addReference('dev_cours_alg_1', $ress);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 1, 15);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 1, 25);
        $ress = $this->createItem($manager,
            'DevoirAlg2',
            'description du devoir 2 en Alg',
            $this->getReference('cours_alg'),
            $dateD,
            $dateF,
            17200
        );
        $this->addReference('dev_cours_alg_2', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours, $dateDebut, $dateFin, $duree){
        $item = new Devoir();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $item->setDateDebut($dateDebut);
        $item->setDateFin($dateFin);
        $item->setDuree($duree);
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
        return 13;
    }
}