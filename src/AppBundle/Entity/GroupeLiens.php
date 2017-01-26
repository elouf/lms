<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * GroupeLiens
 *
 * @ORM\Table(name="groupeliens")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupeLiensRepository")
 */
class GroupeLiens extends Ressource
{

    /**
     * @ORM\OneToMany(targetEntity="AssocGroupeLiens", mappedBy="groupe")
     */
    private $assocLiens;

}
