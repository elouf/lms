<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevoirSujet
 *
 * @ORM\Table(name="devoirSujet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DevoirSujetRepository")
 */
class DevoirSujet extends Fichier
{

    /**
     * @var Devoir
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Devoir", cascade={"persist"})
     */
    private $devoir;

    /**
     * Set devoir
     *
     * @param Devoir $devoir
     *
     * @return DevoirSujet
     */
    public function setDevoir($devoir)
    {
        $this->devoir = $devoir;

        return $this;
    }

    /**
     * Get devoir
     *
     * @return Devoir
     */
    public function getDevoir()
    {
        return $this->devoir;
    }
}
