<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocDocCours
 *
 * @ORM\Table(name="assoc_doc_cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocDocCoursRepository")
 */
class AssocDocCours
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
     * @var Cours
     * @ORM\ManyToOne(targetEntity="Cours", inversedBy="assocDoc")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=false)
     */
    private $cours;

    /**
     * @var Document
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="assocCours")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id", nullable=false)
     */
    private $document;

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return AssocDocCours
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
}
