<?php

namespace AppBundle\DataFixtures\FromChamilo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use mysqli;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChamiloConnect extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    protected $host;
    protected $user;
    protected $password;
    protected $database;
    protected $mysqli;

    /**
     * @var ContainerInterface
     */
    private $container;

    protected $connect;

    public function __construct()
    {

    }

    public function getMysqli()
    {
        return $this->mysqli;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->host = $this->container->getParameter('distant_host');
        $this->user = $this->container->getParameter('distant_user');
        $this->database = $this->container->getParameter('distant_database');
        $this->password = $this->container->getParameter('distant_password');
        $this->connect = mysqli_connect($this->host, $this->user, $this->password, $this->database) or die("Couldn't connect to the destination database!");
        $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);

        /* Vérification de la connexion */
        if (mysqli_connect_errno()) {
            printf("Échec de la connexion : %s\n", mysqli_connect_error());
            exit();
        }
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // TODO: Implement load() method.
    }
}