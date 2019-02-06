<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="log", type="text", nullable=true)
     */
    protected $log;


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
     * __toString method
     */
    public function __toString()
    {
        return (string)$this->getId();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Log
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
     * Set log
     *
     * @param string $log
     *
     * @return Log
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     *
     */
    public function appendLogDate()
    {
        $this->log .= "<br>\n----------------<br>\n[date : ".date("Y-m-d H:i:s")."]<br>\n";
    }

    /**
     *
     */
    public function appendLogType()
    {
        $this->log .= "[type : ".$this->getType()."]<br>\n";
    }

    /**
     * @param string $newlog
     */
    public function appendLog($newlog)
    {
        $this->log .= "<br>\n".$newlog;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Log
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
