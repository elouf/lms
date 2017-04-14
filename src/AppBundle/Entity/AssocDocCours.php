<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocDocCours
 *
 * @ORM\Table(name="assoc_doc_cours")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocDocCoursRepository")
 */
class AssocDocCours extends AssocDocEntity
{

    /**
     * @var Cours
     * @ORM\ManyToOne(targetEntity="Cours")
     * @ORM\JoinColumn(name="cours_id", referencedColumnName="id", nullable=false)
     */
    private $cours;

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
}
