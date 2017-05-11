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
 *     Ressource::TYPE_FORUM = "Forum"
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

    /**
     * @var Cours
     *
     * @ORM\ManyToOne(targetEntity="Cours")
     */
    private $cours;

    abstract public function getType();

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
