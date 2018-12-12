<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatsUsersDocs
 *
 * @ORM\Table(name="statsUsersDocs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatsUsersDocsRepository")
 */
class StatsUsersDocs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAcces", type="datetime")
     */
    protected $dateAcces;

    /**
     * @var Document
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist"})
     */
    protected $document;

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
     * Set user
     *
     * @param User $user
     *
     * @return StatsUsersDocs
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set dateAcces
     *
     * @param \DateTime $dateAcces
     *
     * @return StatsUsersDocs
     */
    public function setDateAcces($dateAcces)
    {
        $this->dateAcces = $dateAcces;

        return $this;
    }

    /**
     * Get dateAcces
     *
     * @return \DateTime
     */
    public function getDateAcces()
    {
        return $this->dateAcces;
    }

    /**
     * Set document
     *
     * @param Document $document
     *
     * @return StatsUsersDocs
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
