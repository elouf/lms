<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocContainer
 * @ORM\MappedSuperclass
 *
 */
class DocContainer
{
    /**
     * @var bool
     *
     * @ORM\Column(name="docsActivated", type="boolean")
     */
    private $docsActivated = false;

    /**
     * Set docsActivated
     *
     * @param boolean $docsActivated
     *
     * @return Cours
     */
    public function setDocsActivated($docsActivated)
    {
        $this->docsActivated = $docsActivated;

        return $this;
    }
    /**
     * Get docsActivated
     *
     * @return bool
     */
    public function getDocsActivated()
    {
        return $this->docsActivated;
    }
}