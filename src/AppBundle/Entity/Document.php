<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
 */
class Document extends Fichier
{

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var User
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     */
    private $proprietaire;

    /**
     * @var Log
     *
     * @ORM\Column(name="preuveEnvoiNotif", type="text", nullable=true)
     */
    protected $preuveEnvoiNotif;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Document
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set preuveEnvoiNotif
     *
     * @param string $preuveEnvoiNotif
     *
     * @return Document
     */
    public function setPreuveEnvoiNotif($preuveEnvoiNotif)
    {
        $this->preuveEnvoiNotif = $preuveEnvoiNotif;

        return $this;
    }

    /**
     * Get preuveEnvoiNotif
     *
     * @return string
     */
    public function getPreuveEnvoiNotif()
    {
        return $this->preuveEnvoiNotif;
    }

    /**
     * Set proprietaire
     *
     * @param User $proprietaire
     *
     * @return Document
     */
    public function setProprietaire($proprietaire)
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return User
     */
    public function getProprietaire()
    {
        return $this->proprietaire;
    }
}
