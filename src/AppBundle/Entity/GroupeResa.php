<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * GroupeResa
 *
 * @ORM\Table(name="resa_groupe")
 * @ORM\Entity
 */
class GroupeResa
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
     *@var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="resas")
     */
    private $users;

    /**
     *
     * @ORM\ManyToOne(targetEntity="SystemeResa", inversedBy="groups")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $system;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVisible", type="boolean", nullable=true)
     */
    private $isVisible;

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
        $this->users = new ArrayCollection();
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getNom();
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return GroupeResa
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
     * @return GroupeResa
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
     * @return mixed
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @param mixed $system
     *
     * @return GroupeResa
     */
    public function setSystem($system)
    {
        $this->system = $system;

        return $this;
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return GroupeResa
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
     * Set users
     *
     * @param array $users
     *
     * @return GroupeResa
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return GroupeResa
     */
    public function addUser($user)
    {
        if(!$this->users->contains($user)){
            $this->users[] = $user;
            $user->addResa($this);
        }

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser($user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeResa($this);
        }
    }
}
