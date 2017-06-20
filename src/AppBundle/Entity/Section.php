<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 */
class Section extends OrderedItem
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVisible", type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * @var Cours
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Cours", cascade={"persist"})
     */
    private $cours;

    /**
     * @var string
     *
     * @ORM\Column(name="faicon", type="string", length=255, unique=false)
     */
    private $faIcon="fa-pencil";

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Section
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return Section
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
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Section
     */
    public function setCours($cours)
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * Get cours
     *
     * @return Cours
     */
    public function getCours()
    {
        return $this->cours;
    }

    /**
     * Set faIcon
     *
     * @param string $faIcon
     *
     * @return Section
     */
    public function setFaIcon($faIcon)
    {
        $this->faIcon = $faIcon;

        return $this;
    }

    /**
     * Get faIcon
     *
     * @return string
     */
    public function getFaIcon()
    {
        return $this->faIcon;
    }
}
