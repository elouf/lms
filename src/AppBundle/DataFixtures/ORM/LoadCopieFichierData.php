<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CopieFichier;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\DevoirSujet;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCopieFichierData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        for($i=1; $i<20; $i++){
            $cop = $this->getReference('copie_dev_cours_alg_2_etu'.$i);

            $dateR = $cop->getDateCreation();
            $ress = $this->createItem($manager,
                'www.google.fr',
                'copie Fichier 1',
                $cop,
                $dateR
            );
            $this->addReference('copieFichier_dev_cours_alg_2_etu'.$i, $ress);
        }

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $url, $nom, $copie, $dateRendu){
        $item = new CopieFichier();
        $item->setUrl($url);
        $item->setCopie($copie);
        $item->setNom($nom);
        $item->setDateRendu($dateRendu);
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
        return 16;
    }
}