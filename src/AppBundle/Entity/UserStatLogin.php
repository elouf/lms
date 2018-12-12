<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStatLogin
 *
 * @ORM\Table(name="UserStatLogin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserStatLoginRepository")
 */
class UserStatLogin
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
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

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
     * @return UserStatLogin
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
     * Set dateAcces
     *
     * @param \DateTime $dateAcces
     *
     * @return UserStatLogin
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
