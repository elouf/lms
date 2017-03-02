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
     * Durée en secondes
     * @var int
     *
     * @ORM\Column(name="corrigeType", type="integer", nullable=true)
     */
    private $corrigeType;

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

    public function getType()
    {
        return $this::TYPE_DEVOIR;
    }
}
