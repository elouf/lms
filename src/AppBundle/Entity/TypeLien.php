<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeLien
 *
 * @ORM\Table(name="typelien")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeLienRepository")
 */
class TypeLien
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="faicon", type="string", length=255, unique=false)
     */
    private $faIcon;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return TypeLien
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
     * Set faIcon
     *
     * @param string $faIcon
     *
     * @return TypeLien
     */
    public function setFaIcon($faIcon)
    {
        $this->faIcon = $faIcon;

        return $this;
    }

    /**
     * Get faIcon
     *
     * @return string
     */
    public function getFaIcon()
    {
        return $this->faIcon;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
