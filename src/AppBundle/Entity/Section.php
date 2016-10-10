<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 */
class Section
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="contentFilePath", type="blob")
     */
    private $contentFilePath;

    /**
     * @var string
     *
     * @ORM\Column(name="pictoFilePath", type="blob")
     */
    private $pictoFilePath;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVisible", type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

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
     * @return Section
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
     * Set contentFilePath
     *
     * @param string $contentFilePath
     *
     * @return Section
     */
    public function setContentFilePath($contentFilePath)
    {
        $this->contentFilePath = $contentFilePath;

        return $this;
    }

    /**
     * Get contentFilePath
     *
     * @return string
     */
    public function getContentFilePath()
    {
        return $this->contentFilePath;
    }

    /**
     * Set pictoFilePath
     *
     * @param string $pictoFilePath
     *
     * @return Section
     */
    public function setPictoFilePath($pictoFilePath)
    {
        $this->pictoFilePath = $pictoFilePath;

        return $this;
    }

    /**
     * Get pictoFilePath
     *
     * @return string
     */
    public function getPictoFilePath()
    {
        return $this->pictoFilePath;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Section
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return Section
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Section
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

