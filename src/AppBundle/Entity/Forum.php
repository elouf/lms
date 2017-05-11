<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Forum
 *
 * @ORM\Table(name="ress_forum")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ForumRepository")
 */
class Forum extends Ressource
{

    public function getType()
    {
        return $this::TYPE_FORUM;
    }
}
