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
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="docsActivated", type="boolean")
     */
    private $docsActivated = false;

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