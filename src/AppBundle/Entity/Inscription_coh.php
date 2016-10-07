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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Cohorte
     *
     * @ORM\ManyToOne(targetEntity="Cohorte")
     */
    private $cohorte;

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

