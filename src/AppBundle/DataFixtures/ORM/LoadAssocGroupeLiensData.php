<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssocGroupeLiens;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssocGroupeLiensData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_1'),
            $this->getReference('groupeLiens_cours_alg_1'),
            "Enoncé",
            $this->getReference('categorielien_intitule'),
            0);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_2'),
            $this->getReference('groupeLiens_cours_alg_1'),
            "Indications (PDF)",
            $this->getReference('categorielien_aide'),
            1);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_3'),
            $this->getReference('groupeLiens_cours_alg_1'),
            "Corrigé (Webmedia)",
            $this->getReference('categorielien_corrige'),
            2);

        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_1'),
            $this->getReference('groupeLiens_cours_alg_2'),
            "Enoncé",
            $this->getReference('categorielien_intitule'),
            0);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_2'),
            $this->getReference('groupeLiens_cours_alg_2'),
            "Indications (PDF)",
            $this->getReference('categorielien_aide'),
            1);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_3'),
            $this->getReference('groupeLiens_cours_alg_2'),
            "Corrigé (Webmedia)",
            $this->getReference('categorielien_corrige'),
            2);

        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_1'),
            $this->getReference('groupeLiens_cours_alg_3'),
            "Enoncé",
            $this->getReference('categorielien_intitule'),
            0);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_2'),
            $this->getReference('groupeLiens_cours_alg_3'),
            "Indications (PDF)",
            $this->getReference('categorielien_aide'),
            1);
        $ress = $this->createItem($manager,
            $this->getReference('ress_cours_alg_3'),
            $this->getReference('groupeLiens_cours_alg_3'),
            "Corrigé (Webmedia)",
            $this->getReference('categorielien_corrige'),
            2);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $lien, $groupe, $nom, $categorieLien, $position){
        $item = new AssocGroupeLiens();
        $item->setLien($lien);
        $item->setGroupe($groupe);
        $item->setNom($nom);
        $item->setCategorieLien($categorieLien);
        $item->setPosition($position);
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