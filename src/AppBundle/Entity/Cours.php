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
class Cours
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
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="blob", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="cout", type="string", length=255, nullable=true)
     */
    private $cout;

    /**
     * @var string
     *
     * @ORM\Column(name="imgFilePath", type="blob", nullable=true)
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
     * @ORM\Column(name="sections", type="array", nullable=true)
     */
    private $sections;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="ressources", type="array", nullable=true)
     */
    private $ressources;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->ressources = new ArrayCollection();
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
     * Set sections
     *
     * @param array $sections
     *
     * @return Cours
     */
    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Add section
     *
     * @param Section  $section
     *
     * @return Cours
     */
    public function addSection(Section $section)
    {
        if(!$this->sections->contains($section)){
            $this->sections[] = $section;
        }
        return $this;
    }
    /**
     * Remove section
     *
     * @param Section  $section
     */
    public function removeSection(Section  $section)
    {
        $this->sections->removeElement($section);
    }
    /**
     * Get sections
     *
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Add ressource
     *
     * @param Ressource  $ressource
     *
     * @return Cours
     */
    public function addRessource(Ressource $ressource)
    {
        if(!$this->ressources->contains($ressource)){
            $this->ressources[] = $ressource;
        }
        return $this;
    }
    /**
     * Remove ressource
     *
     * @param Ressource  $ressource
     */
    public function removeRessource(Ressource  $ressource)
    {
        $this->ressources->removeElement($ressource);
    }

    /**
     * Get ressources
     *
     * @return ArrayCollection
     */
    public function getRessources()
    {
        return $this->ressources;
    }
}

