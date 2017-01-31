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
     * @ORM\ManyToOne(targetEntity="Ressource", cascade={"persist"})
     */
    private $ressource;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVisible", type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * Sera utilisée pour les zones libres surtout
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

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

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return ZoneRessource
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ZoneRessource
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
