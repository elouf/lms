<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocDocDisc
 *
 * @ORM\Table(name="assoc_doc_disc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocDocDiscRepository")
 */
class AssocDocDisc
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
     * @var Discipline
     * @ORM\ManyToOne(targetEntity="Discipline", inversedBy="assocDoc")
     * @ORM\JoinColumn(name="disc_id", referencedColumnName="id", nullable=false)
     */
    private $discipline;

    /**
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="assocDisc")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", nullable=false)
     */
    private $document;

    /**
     * Set discipline
     *
     * @param Discipline $discipline
     *
     * @return AssocDocDisc
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * Set document
     *
     * @param Document $document
     *
     * @return AssocDocDisc
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
}
