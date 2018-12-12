<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evt_user
 *
 * @ORM\Table(name="evt_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvtUserRepository")
 */
class Evt_user extends Evenement
{
    /**
     * @var User
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Evt_user
     */
    public function setUSer($user)
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
}
