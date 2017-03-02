<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evt_cours
 *
 * @ORM\Table(name="evt_cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvtCoursRepository")
 */
class Evt_cours extends Evenement
{
    /**
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Evt_cours
     */
    public function setCours($cours)
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * Get cours
     *
     * @return Cours
     */
    public function getCours()
    {
        return $this->cours;
    }
}
