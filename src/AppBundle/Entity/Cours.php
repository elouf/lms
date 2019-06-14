<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Cours
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoursRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="isSession", type="integer")
 * @ORM\DiscriminatorMap({"0" = "Cours", "1" = "Session"})
 */
class Cours extends DocContainer
{

    const SERVER_PATH_TO_IMAGE_FOLDER = '../web/images/cours';

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
     * @var UploadedFile
     *
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(name="imageFilename", type="string", length=255, unique=false)
     */
    private $imageFilename;

    /**
     * @var Discipline
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     * @var Session
     *
     * @ORM\ManyToOne(targetEntity="Session")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     *
     */
    private $session;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position=0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var User
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $auteur;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default":true})
     */
    private $enabled = true;

    /**
     * Constructor
     */
    public function __construct() {
        $this->cohortes = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="intituleSharedDocs", type="string", length=1024, unique=false, nullable=true)
     */
    private $intituleSharedDocs;

    /**
     * __toString method
     */
    public function __toString()
    {

        if(is_null($this->getNom())){
            return "";
        }else{
            return $this->getNom().' ('.$this->getDiscipline()->getNom().')';
        }
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

    /**
     * Set session
     *
     * @param Session $session
     *
     * @return Cours
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Cours
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets imageFile.
     *
     * @param UploadedFile $imageFile
     */
    public function setImageFile(UploadedFile $imageFile = null)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * Get imageFile
     *
     * @return UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set imageFilename
     *
     * @param string $imageFilename
     *
     * @return Cours
     */
    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    /**
     * Get imageFilename
     *
     * @return string
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * Manages the copying of the imageFile to the relevant place on the server
     */
    public function upload()
    {
        // the imageFile property can be empty if the field is not required
        if (null === $this->getImageFile()) {
            return;
        }

        // we use the original imagefile name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and target filename as params
        $this->getImageFile()->move(
            self::SERVER_PATH_TO_IMAGE_FOLDER,
            $this->getImageFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the imageFile
        $this->setImageFilename($this->getImageFile()->getClientOriginalName());

        // clean up the file property as you won't need it anymore
        $this->setImageFile(null);
    }

    public function getWebPath()
    {
        return self::SERVER_PATH_TO_IMAGE_FOLDER.'/'.$this->getImageFilename();
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleImageFileUpload()
    {
        $this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param DateTime $updated
     * @return Cours
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Set auteur
     *
     * @param User $auteur
     *
     * @return Cours
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return User
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Cours
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set intituleSharedDocs
     *
     * @param string $intituleSharedDocs
     *
     * @return Cours
     */
    public function setIntituleSharedDocs($intituleSharedDocs)
    {
        $this->intituleSharedDocs = $intituleSharedDocs;

        return $this;
    }

    /**
     * Get intituleSharedDocs
     *
     * @return string
     */
    public function getIntituleSharedDocs()
    {
        return $this->intituleSharedDocs;
    }
}
