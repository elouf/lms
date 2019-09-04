<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Cohorte
 *
 * @ORM\Table(name="cohorte")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CohorteRepository")
 */
class Cohorte
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
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     * @Serializer\Groups({"oneUser"})
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Discipline", inversedBy="cohortes")
     * @ORM\JoinTable(name="coh_disc")
     */
    private $disciplines;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Cours", inversedBy="cohortes")
     * @ORM\JoinTable(name="coh_cours")
     */
    private $cours;

    /**
     * Constructor
     */
    public function __construct() {
        $this->disciplines = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getNom();
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
     * @return Cohorte
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
     * @return Cohorte
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
     * Add discipline
     *
     * @param Discipline $discipline
     * @return Cohorte
     */
    public function addDiscipline(Discipline $discipline)
    {
        if(!$this->disciplines->contains($discipline)){
            $this->disciplines[] = $discipline;
        }
        return $this;
    }
    /**
     * Remove discipline
     *
     * @param Discipline $discipline
     */
    public function removeDiscipline(Discipline $discipline)
    {
        $this->disciplines->removeElement($discipline);
    }
    /**
     * Get disciplines
     *
     * @return ArrayCollection
     */
    public function getDisciplines()
    {
        return $this->disciplines;
    }

    /**
     * Add a cours
     *
     * @param Cours $cours
     * @return Cohorte
     */
    public function addCours(Cours $cours)
    {
        if(!$this->cours->contains($cours)){
            $this->cours[] = $cours;
        }
        return $this;
    }
    /**
     * Remove a cours
     *
     * @param Cours $cours
     */
    public function removeCours(Cours $cours)
    {
        $this->cours->removeElement($cours);
    }
    /**
     * Get courses
     *
     * @return ArrayCollection
     */
    public function getCours()
    {
        return $this->cours;
    }
}
