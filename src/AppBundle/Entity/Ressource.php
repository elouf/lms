<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ressource
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="ressource")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     Ressource::TYPE_DEVOIR = "Devoir",
 *     Ressource::TYPE_LIEN = "Lien",
 *     Ressource::TYPE_GROUPE = "GroupeLiens",
 *     Ressource::TYPE_LIBRE = "RessourceLibre",
 *     Ressource::TYPE_FORUM = "Forum",
 *     Ressource::TYPE_CHAT = "Chat",
 *     Ressource::TYPE_H5P = "RessourceH5P",
 *     Ressource::TYPE_PODCAST = "Podcast"
 * })
 *
 */
abstract class Ressource extends Evenement
{
    const TYPE_DEVOIR = 'devoir';
    const TYPE_LIEN    = 'lien';
    const TYPE_GROUPE = 'groupe';
    const TYPE_LIBRE    = 'libre';
    const TYPE_FORUM   = 'forum';
    const TYPE_CHAT   = 'chat';
    const TYPE_H5P   = 'h5p';
    const TYPE_PODCAST   = 'podcast';

    /**
     * @var Cours
     *
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

    abstract public function getType();

    /**
     * Set cours
     *
     * @param Cours $cours
     *
     * @return Ressource
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
