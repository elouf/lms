<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evt_discipline
 *
 * @ORM\Table(name="evt_discipline")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvtDisciplineRepository")
 */
class Evt_discipline extends Evenement
{
    /**
     * @var Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     */
    private $discipline;

    /**
     * Set discipline
     *
     * @param Discipline $discipline
     *
     * @return Evt_discipline
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
