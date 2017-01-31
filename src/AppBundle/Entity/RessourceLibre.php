<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RessourceLibre
 *
 * @ORM\Table(name="ressourceLibre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RessourceLibreRepository")
 */
class RessourceLibre extends Ressource
{
    /*public function __toString()
    {
        return $this->description;
    }*/
}
