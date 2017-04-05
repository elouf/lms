<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDocumentData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        for($i=0; $i<15; $i++) {
            $dateC = new \DateTime();
            $dateC->setDate(2017, 3, $i);

            $ress = $this->createItem($manager,
                'www.google.fr'.$i,
                'Document test '.$i,
                'Cescription du document '.$i,
                $dateC,
                $this->getReference('user_admin')
            );
            $this->addReference('doc_'.$i, $ress);
        }

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $url, $nom, $descr, $dateCrea, User $proprietaire){
        $item = new Document();
        $item->setUrl($url);
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setDateCrea($dateCrea);
        $item->setProprietaire($proprietaire);
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
        return 18;
    }
}