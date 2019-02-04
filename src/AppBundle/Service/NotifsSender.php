<?php

namespace AppBundle\Service;

use AppBundle\Entity\Log;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class NotifsSender
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->cp = new Log();
        $this->cp->setType('Envoi de notifications');
        $this->cp->appendLogType();
    }

    /**
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function submit()
    {
        $this->cp->appendLogDate();
        try {
            $this->em->persist($this->cp);
            $this->em->flush();
        } catch (ORMException $e) {
        }
    }

    public function addLog($msg)
    {
        $this->cp->appendLog($msg);
        try {
            $this->em->persist($this->cp);
            $this->em->flush();
        } catch (ORMException $e) {
        }
    }
}