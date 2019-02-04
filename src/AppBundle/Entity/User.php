<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="$firstname", type="string", length=255)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    protected $lastname;

    /**
     * @var Institut
     *
     * @ORM\ManyToOne(targetEntity="Institut")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $institut;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable = true)
     */
    protected $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     *@var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Chat", mappedBy="administrateurs")
     */
    private $chats;

    /**
     * @var bool
     *
     * @ORM\Column(name="receiveAutoNotifs", type="boolean", nullable=true)
     */
    private $receiveAutoNotifs = true;

    public function __construct()
    {
        parent::__construct();
        $this->chats = new ArrayCollection();
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set Institut
     *
     * @param Institut $institut
     *
     * @return User
     */
    public function setInstitut($institut)
    {
        $this->institut = $institut;

        return $this;
    }

    /**
     * Get institut
     *
     * @return Institut
     */
    public function getInstitut()
    {
        return $this->institut;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail($email)
    {
        parent::setUsername($email);
        return parent::setEmail($email);
    }

    public function setEmailCanonical($emailCanonical)
    {
        parent::setUsernameCanonical($emailCanonical);
        return parent::setEmailCanonical($emailCanonical);
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
     * @return User
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

    /**
     * Add a chat
     *
     * @param Chat $chat
     * @return User
     */
    public function addChat(Chat $chat)
    {
        if(!$this->chats->contains($chat)){
            $this->chats[] = $chat;
        }
        return $this;
    }
    /**
     * Remove a chat
     *
     * @param Chat $chat
     */
    public function removeChat(Chat $chat)
    {
        $this->chats->removeElement($chat);
    }
    /**
     * Get chats
     *
     * @return ArrayCollection
     */
    public function getChats()
    {
        return $this->chats;
    }

    /**
     * Set receiveAutoNotifs
     *
     * @param boolean $receiveAutoNotifs
     *
     * @return User
     */
    public function setReceiveAutoNotifs($receiveAutoNotifs)
    {
        $this->receiveAutoNotifs = $receiveAutoNotifs;

        return $this;
    }

    /**
     * Get receiveAutoNotifs
     *
     * @return bool
     */
    public function getReceiveAutoNotifs()
    {
        return $this->receiveAutoNotifs;
    }
}
