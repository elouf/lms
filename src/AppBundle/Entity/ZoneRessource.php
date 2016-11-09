<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZoneRessource
 *
 * @ORM\Table(name="zone_ressource")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ZoneRessourceRepository")
 */
class ZoneRessource extends OrderedItem
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="Section")
     */
    private $section;

    /**
     * @var Ressource
     *
     * @ORM\ManyToOne(targetEntity="Ressource")
     */
    private $ressource;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set section
     *
     * @param Section $section
     *
     * @return ZoneRessource
     */
    public function setSection($section)
    {
        $this->section = $section;
        return $this;
    }

    /**
     * Get section
     *
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set ressource
     *
     * @param Ressource $ressource
     *
     * @return ZoneRessource
     */
    public function setRessource($ressource)
    {
        $this->ressource = $ressource;
        return $this;
    }

    /**
     * Get ressource
     *
     * @return Ressource
     */
    public function getRessource()
    {
        return $this->ressource;
    }

}
