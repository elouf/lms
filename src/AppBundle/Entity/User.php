<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Institut
     *
     * @ORM\ManyToOne(targetEntity="Institut")
     */
    private $institut;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set Institut
     *
     * @param Institut $institut
     *
     * @return User
     */
    public function setInstitut($institut)
    {
        $this->institut = $institut;

        return $this;
    }

    /**
     * Get institut
     *
     * @return Institut
     */
    public function getInstitut()
    {
        return $this->institut;
    }
}