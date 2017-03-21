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
            'Allemand',
            [],
            []);
        $this->addReference('coh_allmand', $coh);
        $coh = $this->createItem($manager,
            'Anglais',
            [$this->getReference('disc_angl')],
            []);
        $this->addReference('coh_angl', $coh);
        $coh = $this->createItem($manager,
            'ArtsAppl',
            [],
            []);
        $this->addReference('coh_artsAppl', $coh);
        $coh = $this->createItem($manager,
            'ArtsPlast',
            [],
            []);
        $this->addReference('coh_artsPlast', $coh);
        $coh = $this->createItem($manager,
            'Documentation',
            [],
            []);
        $this->addReference('coh_documentation', $coh);
        $coh = $this->createItem($manager,
            'EcoGest',
            [],
            []);
        $this->addReference('coh_ecoGest', $coh);
        $coh = $this->createItem($manager,
            'EducMusChantChoral',
            [],
            []);
        $this->addReference('coh_educMusChantChoral', $coh);
        $coh = $this->createItem($manager,
            'EMCCFOADM1',
            [],
            []);
        $this->addReference('coh_EMCCFOADM1', $coh);
        $coh = $this->createItem($manager,
            'Espagnol',
            [$this->getReference('disc_esp')],
            []);
        $this->addReference('coh_esp', $coh);
        $coh = $this->createItem($manager,
            'HG',
            [$this->getReference('disc_hist')],
            []);
        $this->addReference('coh_hist', $coh);
        $coh = $this->createItem($manager,
            'Lettres',
            [$this->getReference('disc_lettres')],
            []);
        $this->addReference('coh_lettres', $coh);
        $coh = $this->createItem($manager,
            'Maths',
            [$this->getReference('disc_maths')],
            []);
        $this->addReference('coh_maths', $coh);
        $coh = $this->createItem($manager,
            'Philosophie',
            [],
            []);
        $this->addReference('coh_philo', $coh);
        $coh = $this->createItem($manager,
            'PhyChi',
            [$this->getReference('disc_phy')],
            []);
        $this->addReference('coh_phy', $coh);
        $coh = $this->createItem($manager,
            'SES',
            [],
            []);
        $this->addReference('coh_ses', $coh);
        $coh = $this->createItem($manager,
            'SIgenie',
            [],
            []);
        $this->addReference('coh_siGenie', $coh);
        $coh = $this->createItem($manager,
            'STMS',
            [],
            []);
        $this->addReference('coh_stms', $coh);
        $coh = $this->createItem($manager,
            'SVT',
            [$this->getReference('disc_svt')],
            []);
        $this->addReference('coh_svt', $coh);

        $coh = $this->createItem($manager,
            'crpe',
            [$this->getReference('disc_crpe_maths'), $this->getReference('disc_crpe_fra')],
            [$this->getReference('cours_espTrad')]);
        $this->addReference('coh_crpe', $coh);
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