<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Podcast
 *
 * @ORM\Table(name="ress_podcast")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PodcastRepository")
 */
class Podcast extends Ressource
{
    /**
     * @var string
     *
     * @ORM\Column(name="rss", type="text", nullable=true)
     */
    private $rss;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mp3Podcast", mappedBy="podcast")
     */
    private $mp3s;

    /**
     * Set rss
     *
     * @param string $rss
     *
     * @return Podcast
     */
    public function setRss($rss)
    {
        $this->rss = $rss;

        return $this;
    }

    /**
     * Get rss
     *
     * @return string
     */
    public function getRss()
    {
        return $this->rss;
    }

    /**
     * Set mp3s
     *
     * @param ArrayCollection $mp3s
     *
     * @return Podcast
     */
    public function setMp3s($mp3s)
    {
        $this->mp3s = $mp3s;

        return $this;
    }

    /**
     * Get mp3s
     *
     * @return ArrayCollection
     */
    public function getMp3s()
    {
        return $this->mp3s;
    }

    /**
     * Add mp3
     *
     * @param Mp3Podcast $mp3
     *
     * @return Podcast
     */
    public function addMp3($mp3)
    {
        if(!$this->mp3s->contains($mp3)){
            $this->mp3s[] = $mp3;
            $mp3->setPodcast($this);
        }

        return $this;
    }

    /**
     * Remove mp3
     *
     * @param Mp3Podcast $mp3
     *
     * @return Podcast
     */
    public function removeMp3($mp3)
    {
        $this->mp3s->removeElement($mp3);

        return $this;
    }

    public function getType()
    {
        return $this::TYPE_PODCAST;
    }
}
