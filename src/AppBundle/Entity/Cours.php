<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cours
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoursRepository")
 */
class Cours extends DocContainer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\Column(name="accueil", type="text", nullable=true)
     */
    private $accueil;

    /**
     * @var string
     *
     * @ORM\Column(name="cout", type="string", length=255, nullable=true)
     */
    private $cout;

    /**
     * @var string
     *
     * @ORM\Column(name="imgFilePath", type="text", nullable=true)
     */
    private $imgFilePath;

    /**
     * @var Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     */
    private $discipline;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Cohorte", mappedBy="cours")
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
     * @return Cours
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
     * @return Cours
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
     * Set accueil
     *
     * @param string $accueil
     *
     * @return Cours
     */
    public function setAccueil($accueil)
    {
        $this->accueil = $accueil;

        return $this;
    }

    /**
     * Get accueil
     *
     * @return string
     */
    public function getAccueil()
    {
        return $this->accueil;
    }

    /**
     * Set cout
     *
     * @param string $cout
     *
     * @return Cours
     */
    public function setCout($cout)
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * Get cout
     *
     * @return string
     */
    public function getCout()
    {
        return $this->cout;
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
     * Set discipline
     *
     * @param Discipline $discipline
     *
     * @return Cours
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Add a cohorte
     *
     * @param Cohorte $cohorte
     * @return Cours
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
