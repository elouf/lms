<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription_sess
 *
 * @ORM\Table(name="inscription_sess")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Inscription_sessRepository")
 */
class Inscription_sess extends Inscription
{
    /**
     * @var Session
     *
     * @ORM\ManyToOne(targetEntity="Session")
     */
    private $session;

    /**
     * Set session
     *
     * @param Cours $session
     *
     * @return Inscription_sess
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
}
