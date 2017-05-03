<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocUserMsg;
use AppBundle\Entity\Message;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessagerieController extends Controller
{

    /**
     * @Route("/messagerie", name="messagerie")
     */
    public function myMessagesAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $assocsUserMsg = $this->getDoctrine()->getRepository('AppBundle:AssocUserMsg')->findBy(array('user' => $this->getUser()));

        $messages = array();
        foreach($assocsUserMsg as $assoc){
            $msg = $assoc->getMessage();
            if(!in_array($msg, $messages)){
                array_push($messages, array($msg, $assoc->getDateLecture()));
            }
        }


        $cohortes = array();
        $disciplines = array();
        $cours = array();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $this->getUser()));
        $inscrDiscs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('user' => $this->getUser()));
        $inscrCourss = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findBy(array('user' => $this->getUser()));

        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findAll();
            $inscrDiscs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findAll();
            $inscrCourss = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findAll();
        }



        if($inscrCourss) {
            foreach ($inscrCourss as $inscrCours) {
                if(!in_array($inscrCours->getCours(), $cours)) {
                    array_push($cours, $inscrCours->getCours());
                }
            }
        }
        if($inscrDiscs) {
            foreach ($inscrDiscs as $inscrDisc) {
                $disc_parse = $inscrDisc->getDiscipline();
                if(!in_array($disc_parse, $disciplines)) {
                    array_push($disciplines, $disc_parse);

                    $courss = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $disc_parse));
                    if($courss){
                        foreach ($courss as $cours_parse) {
                            if (!in_array($cours_parse, $cours)) {
                                array_push($cours, $cours_parse);
                            }
                        }
                    }
                }
            }
        }
        if($inscrCohs) {
            foreach ($inscrCohs as $inscrCoh) {
                $coh_parse = $inscrCoh->getCohorte();
                if(!in_array($coh_parse, $cohortes)) {
                    array_push($cohortes, $coh_parse);

                    foreach ($coh_parse->getDisciplines() as $disc) {
                        if(!in_array($disc, $disciplines)) {
                            array_push($disciplines, $disc);

                            $courss = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $disc));
                            if($courss){
                                foreach ($courss as $cours_parse) {
                                    if (!in_array($cours_parse, $cours)) {
                                        array_push($cours, $cours_parse);
                                    }
                                }
                            }
                        }
                    }

                    foreach ($coh_parse->getCours() as $cours_parse) {
                        if (!in_array($cours_parse, $cours)) {
                            array_push($cours, $cours_parse);
                        }
                    }
                }
            }
        }

        return $this->render('messagerie.html.twig', [
            'messages' => $messages,
            'cours' => $cours,
            'disciplines' => $disciplines,
            'cohortes' => $cohortes,
            'users' => $users
        ]);
    }

    /**
     * @Route("/getMsg_ajax", name="getMsg_ajax")
     */
    public function getMsgAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $msgId = $request->request->get('id');

            $message = $em->getRepository('AppBundle:Message')->findOneBy(array('id' => $msgId));

            $assocsUserMsg = $this->getDoctrine()->getRepository('AppBundle:AssocUserMsg')->findBy(array('user' => $this->getUser(), 'message' => $message));
            if($assocsUserMsg){
                foreach($assocsUserMsg as $assoc) {
                    $assoc->setDateLecture(new DateTime());
                }
            }

            $em->flush();

            return new JsonResponse(array('action' =>'Get Message',
                'id' => $msgId, 'objet' => $message->getObjet(), 'contenu' => $message->getContenu(), 'expediteur' => $message->getExpediteur(), 'dateCreation' => $message->getDateCreation()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteMsgUser_ajax", name="deleteMsgUser_ajax")
     */
    public function deleteMsgUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $msgId = $request->request->get('id');

            $message = $em->getRepository('AppBundle:Message')->findOneBy(array('id' => $msgId));
            $assocsUserMsg = $this->getDoctrine()->getRepository('AppBundle:AssocUserMsg')->findBy(array('user' => $this->getUser(), 'message' => $message));

            if($assocsUserMsg){
                foreach($assocsUserMsg as $assoc) {
                    $em->remove($assoc);
                }
            }

            $em->flush();

            return new JsonResponse(array('action' =>'deleteMsgUser', 'id' => $msgId, 'user' => $this->getUser()->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getFiltredUsers_ajax", name="getFiltredUsers_ajax")
     */
    public function getFiltredUsersAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $type = $request->request->get('type');
            $id = $request->request->get('idItem');

            $users = array();
            $inscriptions = null;
            $discipline = null;
            $cohorte = null;
            $cours = null;

            if($type == "cohorte"){
                $cohorte = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $id));
                $inscriptions = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
            }else if($type == "discipline"){
                $discipline = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));
                $inscriptions = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
            }else if($type == "cours"){
                $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));
                $inscriptions = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
            }
            if($inscriptions){
                foreach ($inscriptions as $inscription) {
                    $user = $inscription->getUser();
                    if (!in_array($user->getId(), $users)) {
                        array_push($users, $user->getId());
                    }
                }
            }

            if($type == "discipline"){
                $cohortes = $em->getRepository('AppBundle:Cohorte')->findAll();
                if($cohortes){
                    foreach ($cohortes as $coh) {
                        if (in_array($discipline, $coh->getDisciplines()->toArray())) {
                            $inscriptions = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $coh));
                            if($inscriptions){
                                foreach ($inscriptions as $inscription) {
                                    $user = $inscription->getUser();
                                    if (!in_array($user->getId(), $users)) {
                                        array_push($users, $user->getId());
                                    }
                                }
                            }
                        }
                    }
                }
            }else if($type == "cours"){
                $cohortes = $em->getRepository('AppBundle:Cohorte')->findAll();
                if($cohortes){
                    foreach ($cohortes as $coh) {
                        if (in_array($cours, $coh->getCours()->toArray())) {
                            $inscriptions = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $coh));
                            if($inscriptions){
                                foreach ($inscriptions as $inscription) {
                                    $user = $inscription->getUser();
                                    if (!in_array($user->getId(), $users)) {
                                        array_push($users, $user->getId());
                                    }
                                }
                            }
                        }
                    }
                    foreach ($cohortes as $coh) {
                        foreach ($coh->getDisciplines() as $disc) {
                            if($cours->getDiscipline()->getId() == $disc->getId()){
                                $inscriptions = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $coh));
                                if($inscriptions){
                                    foreach ($inscriptions as $inscription) {
                                        $user = $inscription->getUser();
                                        if (!in_array($user->getId(), $users)) {
                                            array_push($users, $user->getId());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $discipline = $cours->getDiscipline();
                $inscriptions = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
                if($inscriptions){
                    foreach ($inscriptions as $inscription) {
                        $user = $inscription->getUser();
                        if (!in_array($user->getId(), $users)) {
                            array_push($users, $user->getId());
                        }
                    }
                }
            }

            $em->flush();

            return new JsonResponse(array('action' =>'get Users filtred', 'users' => $users));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sendMsg_ajax", name="sendMsg_ajax")
     */
    public function sendMsgAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $objet = $request->request->get('objet');
            $contenu = $request->request->get('contenu');
            $users = $request->request->get('users');

            if (!in_array($this->getUser()->getId(), $users)) {
                array_push($users, $this->getUser()->getId());
            }

            $message = new Message();

            $message->setObjet($objet);
            $message->setExpediteur($this->getUser());
            $message->setDateCreation(new DateTime());
            $message->setContenu($contenu);

            $em->persist($message);

            for($i=0; $i<count($users); $i++){
                $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));
                if($user){
                    $assoc = new AssocUserMsg();
                    $assoc->setUser($user);
                    $assoc->setMessage($message);

                    if($user->getId() == $this->getUser()->getId()){
                        $assoc->setDateLecture(new DateTime());
                    }
                    $em->persist($assoc);
                }
            }


            $em->flush();

            return new JsonResponse(array('action' =>'Create message',
                'id' => $message->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getNbMsg_ajax", name="getNbMsg_ajax")
     */
    public function getNbMsgAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $assocsUserMsg = $this->getDoctrine()->getRepository('AppBundle:AssocUserMsg')->findBy(array('user' => $this->getUser(), 'dateLecture' => null));

            $nbMsgs = count($assocsUserMsg);
            $em->flush();

            return new JsonResponse(array('action' =>'Get Nb new Message',
                'nbMsgs' => $nbMsgs));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}