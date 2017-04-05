<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Discipline
 *
 * @ORM\Table(name="discipline")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DisciplineRepository")
 */
class Discipline extends DocContainer
{
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="imgFilePath", type="text", nullable=true)
     */
    private $imgFilePath;

    /**
     *@var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Cohorte", mappedBy="disciplines")
     */
    private $cohortes;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cohortes = new ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Discipline
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
     * Set description
     *
     * @param string $description
     *
     * @return Discipline
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

    /**
     * Set imgFilePath
     *
     * @param string $imgFilePath
     *
     * @return Cours
     */
    public function setImgFilePath($imgFilePath)
    {
        $this->imgFilePath = $imgFilePath;

        return $this;
    }

    /**
     * Get imgFilePath
     *
     * @return string
     */
    public function getImgFilePath()
    {
        return $this->imgFilePath;
    }

    /**
     * Add a cohorte
     *
     * @param Cohorte $cohorte
     * @return Discipline
     */
    public function addCohorte(Cohorte $cohorte)
    {
        if(!$this->cohortes->contains($cohorte)){
            $this->cohortes[] = $cohorte;
        }
        return $this;
    }
    /**
     * Remove a cohorte
     *
     * @param Cohorte $cohorte
     */
    public function removeCohorte(Cohorte $cohorte)
    {
        $this->cohortes->removeElement($cohorte);
    }
    /**
     * Get cohortes
     *
     * @return ArrayCollection
     */
    public function getCohortes()
    {
        return $this->cohortes;
    }

}
