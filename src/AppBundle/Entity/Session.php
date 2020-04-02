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
class Session extends Evenement
{
    /**
     * @var string
     *
     * @ORM\Column(name="messageAlerts", type="text", nullable=true)
     */
    protected $messageAlerte;

    /**
     * @var string
     *
     * @ORM\Column(name="messageFinSession", type="text", nullable=true)
     */
    protected $messageFinSession;

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
     * @var bool
     *
     * @ORM\Column(name="accessOnlyForAdmin", type="boolean", options={"default":false})
     */
    private $accessOnlyForAdmin = false;

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
     * Set messageFinSession
     *
     * @param string $messageFinSession
     *
     * @return Session
     */
    public function setMessageFinSession($messageFinSession)
    {
        $this->messageFinSession = $messageFinSession;

        return $this;
    }

    /**
     * Get messageFinSession
     *
     * @return string
     */
    public function getMessageFinSession()
    {
        return $this->messageFinSession;
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

    /**
     * Set accessOnlyForAdmin
     *
     * @param boolean $accessOnlyForAdmin
     *
     * @return Session
     */
    public function setAccessOnlyForAdmin($accessOnlyForAdmin)
    {
        $this->accessOnlyForAdmin = $accessOnlyForAdmin;

        return $this;
    }

    /**
     * Get accessOnlyForAdmin
     *
     * @return bool
     */
    public function getAccessOnlyForAdmin()
    {
        return $this->accessOnlyForAdmin;
    }
}