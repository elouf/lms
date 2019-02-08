<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssocDocCours;
use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Cohorte;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_coh;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\Log;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class NotifsSender
{
    protected $em;
    protected $dateLimit;
    protected $mailer;
    protected $templating;
    protected $cp;

    public function __construct(\Doctrine\ORM\EntityManager $em, \Swift_Mailer $mailer, \Twig_Environment $templating, $dateLimit)
    {
        /*
        TODO : ajouter la commande dans la crontable
        php /home/users/afadec.demo/html/lms/bin/console app:sendnotifscommand
        */

        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->cp = new Log();
        $this->cp->setType('Envoi de notifications');
        $this->dateLimit = new \DateTime();
        $this->dateLimit->setTimestamp($dateLimit);
    }

    /**
     * @return string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function submit()
    {
        $this->cp->appendLogDate();
        $this->cp->appendLogType();
        try {
            $this->em->persist($this->cp);
            $this->em->flush();
        } catch (ORMException $e) {
        }
        $docs = $this->em->getRepository('AppBundle:Document')->findBy(array('preuveEnvoiNotif' => null));

        /*
             * Format du tableau :
             * ['idUser' =>
             *      [
             *      'docsDisc' =>
             *          ['idDisc' => [idDocs, idDocs, idDocs]],
             *      'docsCours' =>
             *          ['idCours' => [idDocs, idDocs]]
             *      ],
             *      'docsCoh' =>
             *          ['idCoh' => [idDocs, idDocs]]
             *      ]
             * ]
             *
             */
        $tabMailsToSend = [];

        if ($docs) {
            /* @var Document $doc */
            foreach ($docs as $key => $doc) {
                $this->addLog('doc ' . $doc->getId());
                if ($doc->getDateCrea() > $this->dateLimit) {
                    // une notif doit être envoyée, on commence par regarder dans les docs de discipline
                    /* @var AssocDocDisc $assocsDocsDisc */
                    $assocsDocsDisc = $this->em->getRepository('AppBundle:AssocDocDisc')->findOneBy(array('document' => $doc));
                    if ($assocsDocsDisc) {
                        $discipline = $assocsDocsDisc->getDiscipline();
                        $discId = $discipline->getId();
                        $discIdStr = strval($discId);
                        $inscrits = $this->em->getRepository('AppBundle:Discipline')->findInscrits($discId);
                        if ($inscrits) {
                            /* @var User $inscrit */
                            foreach ($inscrits as $inscrit) {
                                $this->appendInUsersTab(strval($inscrit->getId()), $discIdStr, 'docsDisc', $tabMailsToSend, $doc);
                            }
                        }
                    }
                    // Ensuite on regarde dans les docs de cours
                    /* @var AssocDocCours $assocsDocsCours */
                    $assocsDocsCours = $this->em->getRepository('AppBundle:AssocDocCours')->findOneBy(array('document' => $doc));
                    if ($assocsDocsCours) {
                        $cours = $assocsDocsCours->getCours();
                        $coursId = $cours->getId();
                        $coursIdStr = strval($coursId);
                        $inscrits = $this->em->getRepository('AppBundle:Cours')->findInscrits($coursId);
                        if ($inscrits) {
                            /* @var User $inscrit */
                            foreach ($inscrits as $inscrit) {
                                $this->appendInUsersTab(strval($inscrit->getId()), $coursIdStr, 'docsCours', $tabMailsToSend, $doc);
                            }
                        }
                    }

                    // Puis les docs liés à une inscription
                    $assocsDocsInscr = $this->em->getRepository('AppBundle:AssocDocInscr')->findBy(array('document' => $doc));
                    if ($assocsDocsInscr) {
                        /* @var AssocDocInscr $assocDocsInscr */
                        foreach ($assocsDocsInscr as $assocDocsInscr) {
                            $idAssoc = strval($assocDocsInscr->getInscription()->getId());
                            $typeAssoc = null;
                            /* @var Inscription_c $inscr_c */
                            $inscr_c = $this->em->getRepository('AppBundle:Inscription_c')->findOneBy(array('id' => $idAssoc));
                            $user = null;
                            $cibleIdStr = "";
                            if ($inscr_c) {
                                $user = $inscr_c->getUser();
                                $cibleIdStr = strval($inscr_c->getCours()->getId());
                                $typeAssoc = 'Inscription_c';
                            } else {
                                /* @var Inscription_d $inscr_d */
                                $inscr_d = $this->em->getRepository('AppBundle:Inscription_d')->findOneBy(array('id' => $idAssoc));
                                if ($inscr_d) {
                                    $user = $inscr_d->getUser();
                                    $cibleIdStr = strval($inscr_d->getDiscipline()->getId());
                                    $typeAssoc = 'Inscription_d';
                                } else {
                                    /* @var Inscription_coh $inscr_coh */
                                    $inscr_coh = $this->em->getRepository('AppBundle:Inscription_coh')->findOneBy(array('id' => $idAssoc));
                                    if ($inscr_coh) {
                                        $user = $inscr_coh->getUser();
                                        $cibleIdStr = strval($inscr_coh->getCohorte()->getId());
                                        $typeAssoc = 'Inscription_coh';
                                    } else {
                                        $this->addLog('PB : type Inscription non trouvé');
                                    }
                                }
                            }
                            if ($user) {
                                $userIdStr = strval($user->getId());
                                /*if ($assocDocsInscr->getCours()) {
                                    $cibleIdStr = strval($inscr_c->getCours()->getId());
                                    $typeAssoc = 'Inscription_c';
                                    $this->appendInUsersTab($userIdStr, $cibleIdStr, 'docsCours', $tabMailsToSend, $doc);
                                } else {*/
                                // on cherche le type d'inscription
                                if ($typeAssoc == 'Inscription_c') {
                                    $this->appendInUsersTab($userIdStr, $cibleIdStr, 'docsCours', $tabMailsToSend, $doc);
                                } elseif ($typeAssoc == 'Inscription_d') {
                                    $this->appendInUsersTab($userIdStr, $cibleIdStr, 'docsDisc', $tabMailsToSend, $doc);
                                } elseif ($typeAssoc == 'Inscription_coh') {
                                    $this->appendInUsersTab($userIdStr, $cibleIdStr, 'docsCoh', $tabMailsToSend, $doc);
                                } else {
                                    $this->addLog('PB : Inscription non trouvée');
                                }
                                //}
                            }
                        }
                    }
                }
                $doc->setPreuveEnvoiNotif("done");
            }
        }
        $this->addLog(json_encode($tabMailsToSend));

        // on envoi les mails à tout ce petit monde !!!!
        foreach ($tabMailsToSend as $idUser => $userMailsToSend) {
            /* @var User $user */
            $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('id' => $idUser));
            $contenuDocuments = "";
            if (array_key_exists('docsDisc', $userMailsToSend) ||
                array_key_exists('docsCours', $userMailsToSend) ||
                array_key_exists('docsCoh', $userMailsToSend)) {
                $contenuDocuments .= "<p>Des documents ont été déposés à votre intention sur la plateforme :</p><ul>";
                if (array_key_exists('docsDisc', $userMailsToSend)) {
                    foreach ($userMailsToSend['docsDisc'] as $idDisc => $docs) {
                        /* @var Discipline $dis */
                        $dis = $this->em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $idDisc));
                        $nom = $dis->getNom();
                        foreach ($docs as $idDoc) {
                            /* @var Document $doc */
                            $doc = $this->em->getRepository('AppBundle:Document')->findOneBy(array('id' => $idDoc));
                            $contenuDocuments .= "<li>" . $doc->getNom() . " dans la discipline " . $nom . "</li>";
                        }
                    }
                }
                if (array_key_exists('docsCours', $userMailsToSend)) {
                    foreach ($userMailsToSend['docsCours'] as $idCours => $docs) {
                        /* @var Cours $cours */
                        $cours = $this->em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $idCours));
                        $nom = $cours->getNom();
                        foreach ($docs as $idDoc) {
                            /* @var Document $doc */
                            $doc = $this->em->getRepository('AppBundle:Document')->findOneBy(array('id' => $idDoc));
                            $contenuDocuments .= "<li>" . $doc->getNom() . " dans le cours " . $nom . "</li>";
                        }
                    }
                }
                if (array_key_exists('docsCoh', $userMailsToSend)) {
                    foreach ($userMailsToSend['docsCoh'] as $idCoh => $docs) {
                        /* @var Cohorte $coh */
                        $coh = $this->em->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $idCoh));
                        $nom = $coh->getNom();
                        foreach ($docs as $idDoc) {
                            /* @var Document $doc */
                            $doc = $this->em->getRepository('AppBundle:Document')->findOneBy(array('id' => $idDoc));
                            $contenuDocuments .= "<li>" . $doc->getNom() . " en " . $nom . "</li>";
                        }
                    }
                }

                $contenuDocuments .= "</ul></br>";
            }

            //TODO : envoi de mails pour les dépôts de copies

            $this->sendMail($user, $contenuDocuments);
        }
        // envoi de mail à l'admin pour le tenir informé
        /* @var User $admin */
        $admin = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => "erwannig.louf@gmail.com"));
        $this->sendMail($admin, "Ci-dessous les logs d'un envoi groupé de mails :<br><br>".$this->cp->getLog());
    }

    public function appendInUsersTab($userIdStr, $idLien, $strInTab, &$tabMailsToSend, Document $doc)
    {
        if (array_key_exists($userIdStr, $tabMailsToSend)) {
            if (array_key_exists($strInTab, $tabMailsToSend[$userIdStr])) {
                if (array_key_exists($idLien, $tabMailsToSend[$userIdStr][$strInTab])) {
                    array_push($tabMailsToSend[$userIdStr][$strInTab][$idLien], $doc->getId());
                } else {
                    $tabMailsToSend[$userIdStr][$strInTab][$idLien] = [$doc->getId()];
                }
            } else {
                $tabMailsToSend[$userIdStr][$strInTab] = [$idLien => [$doc->getId()]];
            }
        } else {
            $tabMailsToSend[$userIdStr] = [$strInTab => [$idLien => [$doc->getId()]]];
        }
        return $tabMailsToSend;
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

    public function sendMail(User $user, $contenuDocuments)
    {
        if($user->getReceiveAutoNotifs()){
            $email = $user->getEmail();

            $message = \Swift_Message::newInstance()
                ->setSubject('[AFADEC] Document déposé')
                ->setFrom('noreply@afadec.fr')
                ->setTo($email)
                ->setBody(
                    $this->templating->render(
                        'emailNotif.html.twig',
                        array(
                            'prenom' => $user->getFirstname(),
                            'nom' => $user->getLastname(),
                            'id' => $user->getId(),
                            'contenuDocuments' => $contenuDocuments
                        )
                    ),
                    'text/html'
                )
                ->addPart(
                    $this->templating->render(
                        'emailNotif.html.twig',
                        array(
                            'prenom' => $user->getFirstname(),
                            'nom' => $user->getLastname(),
                            'id' => $user->getId(),
                            'contenuDocuments' => $contenuDocuments
                        )
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
            $this->addLog('Send mail : '.$email);
        }
    }
}