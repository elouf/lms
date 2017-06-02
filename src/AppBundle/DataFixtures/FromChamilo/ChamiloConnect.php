<?php

namespace AppBundle\DataFixtures\FromChamilo;


use mysqli;

class ChamiloConnect
{
    protected $host = ;
    protected $user = ;
    protected $password = ;
    protected $database = ;
    protected $mysqli;

    protected $connect;

    public function __construct()
    {
        $this->connect = mysqli_connect($this->host, $this->user, $this->password, $this->database) or die("Couldn't connect to the destination database!");
        $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);

        /* Vérification de la connexion */
        if (mysqli_connect_errno()) {
            printf("Échec de la connexion : %s\n", mysqli_connect_error());
            exit();
        }
    }

    public function getMysqli()
    {
        return $this->mysqli;
    }

}