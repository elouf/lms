<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DevoirCorrigeType
 *
 * @ORM\Table(name="devoirCorrigeType")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DevoirCorrigeTypeRepository")
 */
class DevoirCorrigeType extends Fichier
{

    /**
     * @var Devoir
     *
     * @ORM\ManyToOne(targetEntity="Devoir")
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
