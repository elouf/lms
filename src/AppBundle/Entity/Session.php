<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionRepository")
 *
 */
class Session extends Cours
{

    /**
     * @var string
     *
     * @ORM\Column(name="messageAlerts", type="text", nullable=true)
     */
    protected $messageAlerte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebutAlerte", type="datetime", nullable=true)
     */
    private $dateDebutAlerte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFinAlerte", type="datetime", nullable=true)
     */
    private $dateFinAlerte;


    /**
     * Set messageAlerte
     *
     * @param string $messageAlerte
     *
     * @return Session
     */
    public function setMessageAlerte($messageAlerte)
    {
        $this->messageAlerte = $messageAlerte;

        return $this;
    }

    /**
     * Get messageAlerte
     *
     * @return string
     */
    public function getMessageAlerte()
    {
        return $this->messageAlerte;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Session
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Session
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateDebutAlerte
     *
     * @param \DateTime $dateDebutAlerte
     *
     * @return Session
     */
    public function setDateDebutAlerte($dateDebutAlerte)
    {
        $this->dateDebutAlerte = $dateDebutAlerte;

        return $this;
    }

    /**
     * Get dateDebutAlerte
     *
     * @return \DateTime
     */
    public function getDateDebutAlerte()
    {
        return $this->dateDebutAlerte;
    }

    /**
     * Set dateFinAlerte
     *
     * @param \DateTime $dateFinAlerte
     *
     * @return Session
     */
    public function setDateFinAlerte($dateFinAlerte)
    {
        $this->dateFinAlerte = $dateFinAlerte;

        return $this;
    }

    /**
     * Get dateFinAlerte
     *
     * @return \DateTime
     */
    public function getDateFinAlerte()
    {
        return $this->dateFinAlerte;
    }

    public function isSession()
    {
        return true;
    }
}