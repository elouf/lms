<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription_coh
 *
 * @ORM\Table(name="inscription_coh")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Inscription_cohRepository")
 */
class Inscription_coh extends Inscription
{

    /**
     * @var Cohorte
     *
     * @ORM\ManyToOne(targetEntity="Cohorte")
     * @ORM\JoinColumn(name="cohorte_id", referencedColumnName="id")
     */
    protected $cohorte;

    /**
     * Set cohorte
     *
     * @param Cohorte $cohorte
     *
     * @return Inscription_coh
     */
    public function setCohorte($cohorte)
    {
        $this->cohorte = $cohorte;

        return $this;
    }

    /**
     * Get cohorte
     *
     * @return Cohorte
     */
    public function getCohorte()
    {
        return $this->cohorte;
    }
}
