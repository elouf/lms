<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Devoir
 *
 * @ORM\Table(name="ress_devoir")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DevoirRepository")
 */
class Devoir extends Ressource
{
    /**
     * Durée en secondes
     * @var int
     *
     * @ORM\Column(name="duree", type="integer")
     */
    private $duree;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaireCopieRendue", type="text", nullable=true)
     */
    private $commentaireCopieRendue;

    /**
     * @var int
     *
     * @ORM\Column(name="bareme", type="integer", nullable=false)
     */
    protected $bareme = 0;

    /**
     * Set duree
     *
     * @param integer $duree
     *
     * @return Devoir
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return int
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set commentaireCopieRendue
     *
     * @param string $commentaireCopieRendue
     *
     * @return Devoir
     */
    public function setCommentaireCopieRendue($commentaireCopieRendue)
    {
        $this->commentaireCopieRendue = $commentaireCopieRendue;

        return $this;
    }

    /**
     * Get commentaireCopieRendue
     *
     * @return string
     */
    public function getCommentaireCopieRendue()
    {
        return $this->commentaireCopieRendue;
    }

    public function getType()
    {
        return $this::TYPE_DEVOIR;
    }

    /**
     * Set bareme
     *
     * @param integer $bareme
     *
     * @return Devoir
     */
    public function setBareme($bareme)
    {
        $this->bareme = $bareme;

        return $this;
    }

    /**
     * Get bareme
     *
     * @return integer
     */
    public function getBareme()
    {
        return $this->bareme;
    }
}
