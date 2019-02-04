<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TypeLien;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadTypeLienData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'PDF',
            'fa-file-pdf-o');
        $this->addReference('typelien_pdf', $ress);
        $ress = $this->createItem($manager,
            'Webmedia',
            'fa-file-video-o');
        $this->addReference('typelien_webmedia', $ress);
        $ress = $this->createItem($manager,
            'Opale',
            'fa-book ');
        $this->addReference('typelien_opale', $ress);
        $ress = $this->createItem($manager,
            'HTTP',
            'fa-external-link');
        $this->addReference('typelien_http', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $faIcon){
        $item = new TypeLien();
        $item->setNom($nom);
        $item->setFaIcon($faIcon);
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