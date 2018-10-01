<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="ress_chat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChatRepository")
 */
class Chat extends Ressource
{

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="chats")
     * @ORM\JoinTable(name="chats_admins")
     */
    private $administrateurs;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled = false;

    /**
     * Constructor
     */
    public function __construct() {
        $this->administrateurs = new ArrayCollection();
    }

    public function getType()
    {
        return $this::TYPE_CHAT;
    }

    /**
     * Add administrateur
     *
     * @param User $administrateur
     * @return Chat
     */
    public function addAdministrateur(User $administrateur)
    {
        if(!$this->administrateurs->contains($administrateur)){
            $this->administrateurs[] = $administrateur;
        }
        return $this;
    }
    /**
     * Remove administrateur
     *
     * @param User $administrateur
     */
    public function removeAdministrateur(User $administrateur)
    {
        $this->administrateurs->removeElement($administrateur);
    }
    /**
     * Remove all administrateur
     */
    public function removeAllAdministrateur()
    {
        foreach($this->administrateurs as $administrateur){
            $this->removeAdministrateur($administrateur);
        }
    }
    /**
     * Get administrateurs
     *
     * @return ArrayCollection
     */
    public function getAdministrateurs()
    {
        return $this->administrateurs;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Chat
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
