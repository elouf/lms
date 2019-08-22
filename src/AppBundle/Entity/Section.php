<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Cours", inversedBy="sections")
     */
    private $cours;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAccesConditionne", type="boolean", nullable=true)
     */
    private $isAccesConditionne;

    /**
     * @var string
     *
     * @ORM\Column(name="faicon", type="string", length=255, unique=false)
     */
    private $faIcon="fa-pencil";

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="autorizedSections", cascade={"persist","remove"})
     */
    private $autorizedUsers;

    public function __construct() {
        $this->autorizedUsers = new ArrayCollection();
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
     * Set isAccesConditionne
     *
     * @param boolean $isAccesConditionne
     *
     * @return Section
     */
    public function setIsAccesConditionne($isAccesConditionne)
    {
        $this->isAccesConditionne = $isAccesConditionne;

        return $this;
    }

    /**
     * Get isAccesConditionne
     *
     * @return bool
     */
    public function getIsAccesConditionne()
    {
        return $this->isAccesConditionne;
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

    /**
     * Set pathologies
     *
     * @param array $autorizedUsers
     *
     * @return Section
     */
    public function setAutorizedUsers($autorizedUsers)
    {
        $this->autorizedUsers = $autorizedUsers;

        return $this;
    }

    /**
     * Get autorizedUsers
     *
     * @return ArrayCollection
     */
    public function getAutorizedUsers()
    {
        return $this->autorizedUsers;
    }

    /**
     * Add autorizedUser
     *
     * @param User $autorizedUser
     *
     * @return Section
     */
    public function addautorizedUser($autorizedUser)
    {
        if(!$this->autorizedUsers->contains($autorizedUser)){
            $this->autorizedUsers[] = $autorizedUser;
            $autorizedUser->addAutorizedSection($this);
        }

        return $this;
    }

    /**
     * Remove autorizedUser
     *
     * @param User $autorizedUser
     */
    public function removeAutorizedUser($autorizedUser)
    {
        if ($this->autorizedUsers->contains($autorizedUser)) {
            $this->autorizedUsers->removeElement($autorizedUser);
            $autorizedUser->removeAutorizedSection($this);
        }
    }
}
