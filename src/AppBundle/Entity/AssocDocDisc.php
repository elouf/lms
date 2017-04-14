<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocDocDisc
 *
 * @ORM\Table(name="assoc_doc_disc")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocDocDiscRepository")
 */
class AssocDocDisc extends AssocDocEntity
{

    /**
     * @var Discipline
     * @ORM\ManyToOne(targetEntity="Discipline")
     * @ORM\JoinColumn(name="disc_id", referencedColumnName="id", nullable=false)
     */
    private $discipline;

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
}
