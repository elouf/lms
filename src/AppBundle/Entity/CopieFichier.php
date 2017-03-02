<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CopieFichier
 *
 * @ORM\Table(name="copieFichier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CopieFichierRepository")
 */
class CopieFichier extends Fichier
{

    /**
     * @var Copie
     *
     * @ORM\ManyToOne(targetEntity="Copie")
     */
    private $copie;

    /**
     * Set copie
     *
     * @param Copie $copie
     *
     * @return CopieFichier
     */
    public function setCopie($copie)
    {
        $this->copie = $copie;

        return $this;
    }

    /**
     * Get copie
     *
     * @return Copie
     */
    public function getCopie()
    {
        return $this->copie;
    }
}
