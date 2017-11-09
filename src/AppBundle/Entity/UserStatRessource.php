<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStatRessource
 *
 * @ORM\Table(name="UserStatRessource")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserStatRessourceRepository")
 */
class UserStatRessource
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @var Ressource
     *
     * @ORM\ManyToOne(targetEntity="Ressource")
     */
    protected $ressource;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAcces", type="datetime")
     */
    protected $dateAcces;

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
     * Set user
     *
     * @param User $user
     *
     * @return UserStatRessource
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ressource
     *
     * @param Ressource $ressource
     *
     * @return UserStatRessource
     */
    public function setRessource($ressource)
    {
        $this->ressource = $ressource;

        return $this;
    }

    /**
     * Get ressource
     *
     * @return Ressource
     */
    public function getRessource()
    {
        return $this->ressource;
    }

    /**
     * Set dateAcces
     *
     * @param \DateTime $dateAcces
     *
     * @return UserStatRessource
     */
    public function setDateAcces($dateAcces)
    {
        $this->dateAcces = $dateAcces;

        return $this;
    }

    /**
     * Get dateAcces
     *
     * @return \DateTime
     */
    public function getDateAcces()
    {
        return $this->dateAcces;
    }

}
