<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocCopieCorrecteur
 *
 * @ORM\Table(name="assoc_copie_correcteur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocCopieCorrecteurRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AssocCopieCorrecteur
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
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="correcteur_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $correcteur;

    /**
     * @var Copie
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OneToOne(targetEntity="Copie", inversedBy="assocCorrecteur")
     */
    private $copie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable = true)
     */
    private $updatedAt;

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
     * Set copie
     *
     * @param Copie $copie
     *
     * @return AssocCopieCorrecteur
     */
    public function setCopie($copie)
    {
        $this->copie = $copie;

        return $this;
    }

    /**
     * Get copie
     *
     * @return Copie
     */
    public function getCopie()
    {
        return $this->copie;
    }

    /**
     * Set correcteur
     *
     * @param User $correcteur
     *
     * @return AssocCopieCorrecteur
     */
    public function setCorrecteur($correcteur)
    {
        $this->correcteur = $correcteur;

        return $this;
    }

    /**
     * Get correcteur
     *
     * @return User
     */
    public function getCorrecteur()
    {
        return $this->correcteur;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return AssocCopieCorrecteur
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return AssocCopieCorrecteur
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
