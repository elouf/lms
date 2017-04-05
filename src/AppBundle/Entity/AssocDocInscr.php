<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocDocInscr
 *
 * @ORM\Table(name="assoc_doc_inscription")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocDocInscrRepository")
 */
class AssocDocInscr
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
     * @var Inscription
     * @ORM\ManyToOne(targetEntity="Inscription", inversedBy="assocDoc")
     * @ORM\JoinColumn(name="inscription_id", referencedColumnName="id", nullable=false)
     */
    private $inscription;

    /**
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="assocInscription")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", nullable=false)
     */
    private $document;

    /**
     * Set inscription
     *
     * @param Inscription $inscription
     *
     * @return AssocDocInscr
     */
    public function setInscription($inscription)
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * Get inscription
     *
     * @return Inscription
     */
    public function getInscription()
    {
        return $this->inscription;
    }

    /**
     * Set document
     *
     * @param Document $document
     *
     * @return AssocDocInscr
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
