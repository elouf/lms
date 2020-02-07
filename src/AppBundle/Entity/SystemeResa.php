<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SystemeResa
 *
 * @ORM\Table(name="resa_system")
 * @ORM\Entity
 */
class SystemeResa
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GroupeResa", mappedBy="system")
     */
    private $groups;

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
     * @ORM\ManyToOne(targetEntity="Cours", inversedBy="resaSystems")
     */
    private $cours;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getNom();
    }


    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return SystemeResa
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

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
     * Set cours
     *
     * @param Cours $cours
     *
     * @return SystemeResa
     */
    public function setCours($cours)
    {
        $this->cours = $cours;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return SystemeResa
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
     * @return SystemeResa
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
     * Set groups
     *
     * @param ArrayCollection $groups
     *
     * @return SystemeResa
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add group
     *
     * @param GroupeResa $group
     *
     * @return SystemeResa
     */
    public function addGroup($group)
    {
        if(!$this->groups->contains($group)){
            $this->groups[] = $group;
            $group->setSystem($this);
        }

        return $this;
    }

    /**
     * Remove group
     *
     * @param GroupeResa $group
     *
     * @return SystemeResa
     */
    public function removeGroup($group)
    {
        $this->groups->removeElement($group);

        return $this;
    }
}
