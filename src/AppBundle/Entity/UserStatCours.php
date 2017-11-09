<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStatCours
 *
 * @ORM\Table(name="UserStatCours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserStatCoursRepository")
 */
class UserStatCours
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
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    protected $cours;

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
     * @return UserStatCours
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
     * Set cours
     *
     * @param Cours $cours
     *
     * @return UserStatCours
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
     * Set dateAcces
     *
     * @param \DateTime $dateAcces
     *
     * @return UserStatCours
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
