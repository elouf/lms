<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription
 * @ORM\MappedSuperclass
 *
 */
class AssocDocEntity
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
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $document;

    /**
     * @var bool
     *
     * @ORM\Column(name="isImportant", type="boolean", nullable=true)
     */
    private $isImportant;

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
     * Set document
     *
     * @param Document $document
     *
     * @return AssocDocCours
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set isImportant
     *
     * @param boolean $isImportant
     *
     * @return AssocDocEntity
     */
    public function setIsImportant($isImportant)
    {
        $this->isImportant = $isImportant;

        return $this;
    }

    /**
     * Get isImportant
     *
     * @return bool
     */
    public function getIsImportant()
    {
        return $this->isImportant;
    }
}
