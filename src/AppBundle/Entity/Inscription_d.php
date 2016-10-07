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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Discipline
     *
     * @ORM\ManyToOne(targetEntity="Discipline")
     */
    private $discipline;


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

