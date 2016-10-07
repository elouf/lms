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

