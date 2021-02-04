<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription_c
 *
 * @ORM\Table(name="inscription_c")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Inscription_cRepository")
 */
class Inscription_c extends Inscription
{
    /**
     * @var Cours
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Inscription_c
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
