<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AssocGroupeLiens
 *
 * @ORM\Table(name="assoc_groupe_liens")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocGroupeLiensRepository")
 */
class AssocGroupeLiens extends OrderedItem
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
     * @ORM\ManyToOne(targetEntity="Lien", inversedBy="assocGroupes")
     * @ORM\JoinColumn(name="lien_id", referencedColumnName="id", nullable=false)
     */
    private $lien;

    /**
     * @ORM\ManyToOne(targetEntity="GroupeLiens", inversedBy="assocLiens")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $groupe;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=false)
     */
    private $nom;

    /**
     * @var CategorieLien
     *
     * @ORM\ManyToOne(targetEntity="CategorieLien")
     */
    protected $categorieLien;

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
     * Set lien
     *
     * @param string $lien
     *
     * @return AssocGroupeLiens
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return Lien
     */
    public function getLien()
    {
        return $this->lien;
    }

    /**
     * Set groupe
     *
     * @param string $groupe
     *
     * @return AssocGroupeLiens
     */
    public function setGroupe($groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get lien
     *
     * @return GroupeLiens
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return AssocGroupeLiens
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set categorieLien
     *
     * @param CategorieLien $categorieLien
     *
     * @return AssocGroupeLiens
     */
    public function setCategorieLien($categorieLien)
    {
        $this->categorieLien = $categorieLien;

        return $this;
    }

    /**
     * Get categorieLien
     *
     * @return CategorieLien
     */
    public function getCategorieLien()
    {
        return $this->categorieLien;
    }
}
