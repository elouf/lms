<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocUserMsg
 *
 * @ORM\Table(name="assoc_user_msg")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocUserMsgRepository")
 */
class AssocUserMsg
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Message
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateLecture", type="datetime", nullable=true)
     */
    protected $dateLecture;

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
     * @return AssocUserMsg
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
     * Set message
     *
     * @param Message $message
     *
     * @return AssocUserMsg
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set dateLecture
     *
     * @param \DateTime $dateLecture
     *
     * @return message
     */
    public function setDateLecture($dateLecture)
    {
        $this->dateLecture = $dateLecture;

        return $this;
    }

    /**
     * Get dateLecture
     *
     * @return \DateTime
     */
    public function getDateLecture()
    {
        return $this->dateLecture;
    }
}
