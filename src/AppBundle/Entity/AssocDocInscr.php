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
     * @ORM\ManyToOne(targetEntity="Inscription")
     * @ORM\JoinColumn(name="inscription_id", referencedColumnName="id", nullable=false)
     */
    private $inscription;

    /**
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $document;

    /**
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $cours;

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

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return AssocDocInscr
     */
    public function setCours($cours)
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * Get cours
     *
     * @return Cours
     */
    public function getCours()
    {
        return $this->cours;
    }
}
