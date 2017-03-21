<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CorrigeFichier
 *
 * @ORM\Table(name="corrigeFichier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorrigeFichierRepository")
 */
class CorrigeFichier extends Fichier
{

    /**
     * @var Corrige
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Corrige", cascade={"persist"})
     */
    private $corrige;

    /**
     * Set corrige
     *
     * @param Corrige $corrige
     *
     * @return CorrigeFichier
     */
    public function setCorrige($corrige)
    {
        $this->corrige = $corrige;

        return $this;
    }

    /**
     * Get corrige
     *
     * @return Corrige
     */
    public function getCorrige()
    {
        return $this->corrige;
    }
}
