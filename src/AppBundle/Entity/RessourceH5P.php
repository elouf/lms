<?php

namespace AppBundle\Entity;

use Emmedy\H5PBundle\Form\Type\H5pType;
use Doctrine\ORM\Mapping as ORM;

/**
 * RessourceH5P
 *
 * @ORM\Table(name="ress_ressourceH5p")
 * @ORM\Entity
 */
class RessourceH5P extends Ressource
{
    /**
     * @ORM\OneToOne(targetEntity="\Emmedy\H5PBundle\Entity\Content")
     * @ORM\JoinColumn(name="h5p_id", referencedColumnName="id")
     */
    private $h5p;


    public function getType()
    {
        return $this::TYPE_H5P;
    }


    /**
     * @return \Emmedy\H5PBundle\Entity\Content
     */
    public function getH5p()
    {
        return $this->h5p;
    }

    /**
     * @param \Emmedy\H5PBundle\Entity\Content $h5p
     */
    public function setH5p($h5p)
    {
        $this->h5p = $h5p;
    }
}
