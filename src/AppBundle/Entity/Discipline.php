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
     * @ORM\Column(name="imgFilePath", type="text", nullable=true)
     */
    private $imgFilePath;

    /**
     * @var string
     *
     * @ORM\Column(name="podcastImgFilename", type="string", length=255, unique=false)
     */
    private $podcastImgFilename;

    /**
     *@var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Cohorte", mappedBy="disciplines")
     */
    private $cohortes;

    /**
     * @var string
     *
     * @ORM\Column(name="faicon", type="string", length=255, unique=false)
     */
    private $faIcon="fa-university";

    /**
     * @var string
     *
     * @ORM\Column(name="accronyme", type="string", length=255, unique=false)
     */
    private $accronyme;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cohortes = new ArrayCollection();
        $this->accronyme = $this->getId();
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

    /**
     * Set faIcon
     *
     * @param string $faIcon
     *
     * @return Discipline
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

    /**
     * Set accronyme
     *
     * @param string $accronyme
     *
     * @return Discipline
     */
    public function setAccronyme($accronyme)
    {
        $this->accronyme = $accronyme;

        return $this;
    }

    /**
     * Get accronyme
     *
     * @return string
     */
    public function getAccronyme()
    {
        return $this->accronyme;
    }

    /**
     * Set podcastImgFilename
     *
     * @param string $podcastImgFilename
     *
     * @return Discipline
     */
    public function setPodcastImgFilename($podcastImgFilename)
    {
        $this->podcastImgFilename = $podcastImgFilename;

        return $this;
    }

    /**
     * Get podcastImgFilename
     *
     * @return string
     */
    public function getPodcastImgFilename()
    {
        return $this->podcastImgFilename;
    }
}
