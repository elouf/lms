<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GroupeResa;
use AppBundle\Entity\SystemeResa;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Forms;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine();

        $myEvents = null;
        $myDiscs = null;
        if ($this->getUser()) {
            $myEvents = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['events'];
            $myDiscs = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['myDiscs'];
        }

        return $this->render('index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            'events' => $myEvents,
            'myDiscs' => $myDiscs,
            'total' => false
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction(Request $request)
    {
        return $this->render('pagesFixes/faq.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/infosPratiques", name="infosPratiques")
     */
    public function infosPratiquesAction(Request $request)
    {
        return $this->render('pagesFixes/infosPratiques.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/concours", name="concours")
     */
    public function concoursAction(Request $request)
    {
        return $this->render('pagesFixes/concours.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/cookies", name="cookies")
     */
    public function cookiesAction(Request $request)
    {
        return $this->render('pagesFixes/cookies.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/metierEnseignant", name="metierEnseignant")
     */
    public function metierEnseignantAction(Request $request)
    {
        return $this->render('pagesFixes/metierEnseignant.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/afadec", name="afadec")
     */
    public function afadecAction(Request $request)
    {
        return $this->render('pagesFixes/afadec.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/deleteCompte", name="pageSuppressionCompte")
     */
    public function pageSuppressionCompteAction(Request $request)
    {
        return $this->render('pagesFixes/pageSuppressionCompte.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/desactivationGroupe", name="desactivationGroupe") methods={"GET", "POST"}
     */
    public function pageDesactivation(Request $request)
    {

        //Création du formulaire d'envoi
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('fichier', FileType::class, ['label' => 'Ajouter mon fichier'])
            ->getForm()
            ;

        $users = [];

        //Le formulaire a été envoyé
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            //On récupère les infos du fichier
            $fichier = $form->get('fichier')->getData();

            //On récupère les users dans la bdd
            $entityManager = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(User::class);

            //On lit le fichier CSV
            if (($handle = fopen($fichier, "r")) !== FALSE) {
                //Pour chaque user du fichier
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    //Ligne du tableau
                    $user = [];

                    //Prénom et nom
                    $num = count($data);
                    for ($c=0; $c < $num; $c++) {
                        array_push($user, $data[$c]);
                    }

                    //Statut (existe ou non dans la bdd)
                    $statut = 'inexistant';
                    $id=null;
                    $mail=null;

                    //Si l'utilisateur existe
                    $utilisateur = $repository->findBy(['firstname' => $data[0], 'lastname' => $data[1]]);

                    dump($utilisateur);

                    if(count($utilisateur) == 1) {

                        //On récupère ses infos
                        $statut = 'desactive';
                        $id = $utilisateur[0]->getId();
                        $mail = $utilisateur[0]->getEmail();

                        //On désactive son compte
                        $utilisateur[0]->setEnabled(0);
                        $entityManager->flush();

                        //On lui envoi un mail
                        $message = \Swift_Message::newInstance()
                            ->setSubject('[AFADEC] Désactivation de votre compte')
                            ->setFrom('noreply@afadec.fr')
                            ->setTo($utilisateur[0]->getEmail())
                            ->setBody(
                                $this->renderView(
                                    'user/desactivationCompteMail.html.twig',
                                    array(
                                        'user' => $utilisateur[0]
                                    )
                                ),
                                'text/html'
                            )
                        ;

                        $this->get('mailer')->send($message);

                    } else if(count($utilisateur) > 1) {
                        $statut = 'doublon';
                    } else {
                        $statut = 'inexistant';
                    }

                    array_push($user, $statut, $id, $mail);

                    //Ajout de la ligne au tableau
                    array_push($users, $user);

                }
                fclose($handle);
            }

            dump($users);

        }

        return $this->render('pagesFixes/desactivationGroupe.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            'form' => $form->createView(),
            'users' => $users
        ]);
    }

    /**
     * @Route("/traitementCSV", name="traitementCSV", methods={"GET"})
     */
    public function traitementCSV(Request $request, $users)
    {
        return $this->render('pagesFixes/traitementCSV.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            'users' => $arrayMembre
        ]);
    }

    /**
     * @Route("/inscrGroupeResa_ajax", name="inscrGroupeResa_ajax")
     */
    public function inscrGroupeResaAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idGroup = $request->request->get('idGroupe');

            /* @var $user User */
            $user = $this->getUser();

            /* @var $group GroupeResa */
            $group = $this->getDoctrine()->getRepository('AppBundle:GroupeResa')
                ->findOneBy(array('id' => $idGroup));
            /* @var $system SystemeResa */
            $system = $group->getSystem();


            /* @var $oneGroup GroupeResa */
            foreach ($system->getGroups() as $oneGroup) {
                $oneGroup->removeUser($user);
            }
            $group->addUser($user);
            $em->flush();

            $arraySystem = [];
            $userGroupId = 0;
            foreach ($system->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desinscrGroupeResa_ajax", name="desinscrGroupeResa_ajax")
     */
    public function desinscrGroupeResaAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idGroup = $request->request->get('idGroupe');

            /* @var $user User */
            $user = $this->getUser();

            /* @var $group GroupeResa */
            $group = $this->getDoctrine()->getRepository('AppBundle:GroupeResa')
                ->findOneBy(array('id' => $idGroup));
            $group->removeUser($user);

            $em->flush();

            $arraySystem = [];
            $userGroupId = 0;
            foreach ($group->getSystem()->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getGroupeResasNumbers_ajax", name="getGroupeResasNumbers_ajax")
     */
    public function getGroupeResasNumbersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $idSystem = $request->request->get('idSystem');

            /* @var $system SystemeResa */
            $system = $this->getDoctrine()->getRepository('AppBundle:SystemeResa')
                ->findOneBy(array('id' => $idSystem));

            $arraySystem = [];
            $userGroupId = 0;
            /* @var $oneGroup GroupeResa */
            foreach ($system->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
