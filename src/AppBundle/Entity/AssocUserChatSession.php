<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocUserChatSession
 *
 * @ORM\Table(name="assoc_user_chatSess")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocUserChatSessionRepository")
 */
class AssocUserChatSession
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="session", type="string", length=255, unique=true)
     */
    private $session;

    /**
     * @var Chat
     *
     * @ORM\ManyToOne(targetEntity="Chat")
     */
    protected $chat;

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
     * Set chat
     *
     * @param Chat $chat
     *
     * @return AssocUserChatSession
     */
    public function setChat($chat)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return Chat
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return AssocUserChatSession
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
     * Set session
     *
     * @param string $session
     *
     * @return AssocUserChatSession
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get session
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

}
