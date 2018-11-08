<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * GroupeLiens
 *
 * @ORM\Table(name="ress_groupeliens")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupeLiensRepository")
 */
class GroupeLiens extends Ressource
{
    /**
     * @ORM\OneToMany(targetEntity="AssocGroupeLiens", mappedBy="groupe", cascade={"persist", "remove"})
     */
    protected $assocLiens;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVertical", type="boolean", nullable=true)
     */
    private $isVertical = false;

    /**
     * Set assocLiens
     *
     * @param User $assocLiens
     *
     * @return GroupeLiens
     */
    public function setAssocLiens($assocLiens)
    {
        $this->auteur = $assocLiens;

        return $this;
    }

    /**
     * Get assocLiens
     *
     * @return AssocGroupeLiens
     */
    public function getAssocLiens()
    {
        return $this->assocLiens;
    }

    public function getType()
    {
        return $this::TYPE_GROUPE;
    }

    /**
     * Set isVertical
     *
     * @param boolean $isVertical
     *
     * @return GroupeLiens
     */
    public function setIsVertical($isVertical)
    {
        $this->isVertical = $isVertical;

        return $this;
    }

    /**
     * Get isVertical
     *
     * @return bool
     */
    public function getIsVertical()
    {
        return $this->isVertical;
    }
}
