<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Forum
 *
 * @ORM\Table(name="forum_sujet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ForumSujetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ForumSujet
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
     * @ORM\Column(name="titre", type="string", length=255)
     */
    protected $titre;

    /**
     * @var bool
     *
     * @ORM\Column(name="ouvert", type="boolean")
     */
    private $ouvert = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="epingle", type="boolean")
     */
    private $epingle = false;

    /**
     * @var Forum
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Forum")
     */
    private $forum;

    /**
     * @var User
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $createur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

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
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getTitre();
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return ForumSujet
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set ouvert
     *
     * @param boolean $ouvert
     *
     * @return ForumSujet
     */
    public function setOuvert($ouvert)
    {
        $this->ouvert = $ouvert;

        return $this;
    }
    /**
     * Get ouvert
     *
     * @return bool
     */
    public function getOuvert()
    {
        return $this->ouvert;
    }

    /**
     * Set epingle
     *
     * @param boolean $epingle
     *
     * @return ForumSujet
     */
    public function setEpingle($epingle)
    {
        $this->epingle = $epingle;

        return $this;
    }
    /**
     * Get epingle
     *
     * @return bool
     */
    public function getEpingle()
    {
        return $this->epingle;
    }

    /**
     * Set forum
     *
     * @param Forum $forum
     *
     * @return ForumSujet
     */
    public function setForum($forum)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return Forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set createur
     *
     * @param User $createur
     *
     * @return ForumSujet
     */
    public function setCreateur($createur)
    {
        $this->createur = $createur;

        return $this;
    }

    /**
     * Get createur
     *
     * @return User
     */
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ForumSujet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
