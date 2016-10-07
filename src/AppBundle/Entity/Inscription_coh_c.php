<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription_coh_c
 *
 * @ORM\Table(name="inscription_coh_c")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Inscription_coh_cRepository")
 */
class Inscription_coh_c
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
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

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
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Inscription_coh_c
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

