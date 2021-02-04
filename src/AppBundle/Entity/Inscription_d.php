<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription_d
 *
 * @ORM\Table(name="inscription_d")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Inscription_dRepository")
 */
class Inscription_d extends Inscription
{

    /**
     * @var Discipline
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Discipline")
     */
    private $discipline;

    /**
     * Set discipline
     *
     * @param Discipline $discipline
     *
     * @return Inscription_d
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }
}
