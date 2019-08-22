<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_d;
use AppBundle\Repository\DisciplineRepository;
use AppBundle\Repository\InstitutRepository;
use DateTime;
use AppBundle\Entity\Inscription_coh;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InscriptionController extends Controller
{

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscriptionAction (Request $request)
    {
        date_default_timezone_set('Europe/Paris');
        $user = new User();
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, array(
            ))
            ->add('prenom', TextType::class, array(
            ))
            ->add('email', EmailType::class, array(
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Numéro de téléphone',
                'required' => false
            ))
            ->add('mdp', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'required' => true,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe')
                ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
                'query_builder' => function (InstitutRepository $in) {
                    return $in->createQueryBuilder('i')
                        ->orderBy('i.nom', 'ASC')
                        ->where('i.actif = true');
                },
                'choice_label' => 'nom',
                'placeholder' => '',
                'multiple' => false,
                ))
            ->add('typeUser', ChoiceType::class, array(
                'choices'  => $user->getStatuts(),
                'label' => 'Vous êtes',
            ))
            ->add('concours', ChoiceType::class, array(
                'choices'  => array(
                    '1er Degré' => 0,
                    '2nd Degré' => 1
                ),
                'label' => 'Concours préparé',
            ))
            ->add('sectionEns', ChoiceType::class, array(
                'choices'  => array(
                    '1er Degré' => 0,
                    '2nd Degré' => 1
                ),
                'label' => "Section d'enseignement",
            ))
            ->add('matiereEtu', ChoiceType::class, array(
                'choices'  => array(
                    'Anglais' => 'Anglais',
                    'Education musicale et chant choral' => 'EducMusChantChoral',
                    'Espagnol' => 'Espagnol',
                    'Histoire-Géographie' => 'HG',
                    'Lettres modernes' => 'LettresModernes',
                    'Mathématiques' => 'Maths',
                    'Physique-Chimie' => 'PhyChi',
                    'SVT' => 'SVT'
                ),
                'label' => "Matière",
            ))
            ->add('matiereForm', ChoiceType::class, array(
                'choices'  => array(
                    'Anglais' => 'Anglais',
                    'Anglais M2' => 'AnglaisM2',
                    'Arts appliqués' => 'ArtsAppl',
                    'Arts plastiques' => 'ArtsPlast',
                    'Biotechnologies (CAPET/CAPLP)' => 'Biotech',
                    'Documentation' => 'Documentation',
                    'Economie Gestion (CAPET)' => 'ECOCAPET',
                    'Economie Gestion (CAPLP)' => 'ECOCAPLP',
                    'Education musicale et chant choral' => 'EducMusChantChoral',
                    'EMCC' => 'EMCC',
                    'EPS' => 'EPS',
                    'Espagnol' => 'Espagnol',
                    'Espagnol M2' => 'EspagnolM2',
                    'Génies' => 'GENIE',
                    'Histoire-Géographie' => 'HG',
                    'Histoire-Géographie M2' => 'HGM2',
                    'Hôtellerie Restauration' => 'HOTEL',
                    'Interlangues' => 'Interlangues',
                    'Lettres' => 'Lettres',
                    'Lettres Histoire-Géographie' => 'LHG',
                    'Lettres Langues' => 'LL',
                    'Lettres modernes' => 'LettresModernes',
                    'Mathématiques' => 'Maths',
                    'Mathématiques M2' => 'MathsM2',
                    'Mathématiques Sciences' => 'MathsSC',
                    'Philosophie' => 'Philosophie',
                    'Physique-Chimie M2' => 'PhyChiM2',
                    'SES' => 'SES',
                    'SII' => 'SII',
                    'Physique-Chimie' => 'PhyChi',
                    'STMS (CAPET)' => 'STMSCA',
                    'STMS (CAPLP)' => 'STMSPLP',
                    'SVT' => 'SVT',
                    'SVT M2' => 'SVTM2'
                ),
                'label' => "Matière",
            ))
            ->add('matiereProfStag', ChoiceType::class, array(
                'choices'  => array(
                    'Anglais' => 'AnglaisM2',
                    'Arts appliqués' => 'ArtsAppl',
                    'Arts plastiques' => 'ArtsPlast',
                    'Biotechnologies (CAPET/CAPLP)' => 'Biotech',
                    'Documentation' => 'Documentation',
                    'Economie Gestion (CAPET)' => 'ECOCAPET',
                    'Economie Gestion (CAPLP)' => 'ECOCAPLP',
                    'EMCC' => 'EMCC',
                    'EPS' => 'EPS',
                    'Espagnol' => 'EspagnolM2',
                    'Génies' => 'GENIE',
                    'Histoire-Géographie' => 'HGM2',
                    'Hôtellerie Restauration' => 'HOTEL',
                    'Interlangues' => 'Interlangues',
                    'Lettres' => 'Lettres',
                    'Lettres Histoire-Géographie' => 'LHG',
                    'Lettres Langues' => 'LL',
                    'Mathématiques' => 'MathsM2',
                    'Mathématiques Sciences' => 'MathsSC',
                    'Philosophie' => 'Philosophie',
                    'Physique-Chimie' => 'PhyChiM2',
                    'SES' => 'SES',
                    'SII' => 'SII',
                    'STMS (CAPET)' => 'STMSCA',
                    'STMS (CAPLP)' => 'STMSPLP',
                    'SVT' => 'SVTM2'
                ),
                'label' => "Matière",
            ))
            /*->add('optionsDisc', ChoiceType::class, array(
                'choices'  => array(
                    'Aucune' => '0',
                    'Anglais niveau B2 (gratuit)' => 'English',
                    'Langues Tell me More (payant)' => 'Langues Tell me More'
                ),
                'label' => "Options",
                'label_attr' => array('class' => 'col-sm-4')
            ))*/
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider mon inscription',
                'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $checkUser = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $data['email']));
            if(!$checkUser){
                $user = new User();

                $user->setFirstname($data['prenom']);
                $user->setLastname($data['nom']);
                $user->setEmail($data['email']);
                $user->setPlainPassword($data['mdp']);
                $user->setPhone($data['phone']);
                $user->setInstitut($data['institut']);
                $user->setStatut($data['typeUser']);

                $em->persist($user);

                $nomCoh = "";
                $role = "";
                if($data['typeUser'] == 'Etudiant'){
                    // Etudiant
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));
                    if($data['concours'] == 0){
                        // CRPE étudiant
                        $nomCoh = 'crpe';
                    }elseif($data['concours'] == 1){
                        // CAPES étudiant
                        $nomCoh = $data['matiereEtu'];
                    }
                    $user->setConfirmedByAdmin(true);
                }elseif($data['typeUser'] == 'Formateur' || $data['typeUser'] == 'Responsable'){
                    // Formateur
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Formateur'));
                    if($data['sectionEns'] == 0){
                        // CRPE formateur
                        $nomCoh = 'crpe';
                    }elseif($data['sectionEns'] == 1){
                        // CAPES étudiant
                        $nomCoh = $data['matiereForm'];
                    }
                    $user->setConfirmedByAdmin(false);
                }elseif($data['typeUser'] == 'Prof_stagiaire'){
                    // Prof stagiaire
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Stagiaire'));
                    $nomCoh = $data['matiereProfStag'];
                    $user->setConfirmedByAdmin(true);
                }
                $coh = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('nom' => $nomCoh));
                $inscr = new Inscription_coh();
                $inscr->setUser($user);
                $inscr->setCohorte($coh);
                $inscr->setDateInscription(new DateTime());
                $inscr->setRole($role);
                $em->persist($inscr);

                $em->flush();

                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $routeName = $request->get('_route');

                $message = \Swift_Message::newInstance()
                    ->setSubject('[AFADEC] Confirmation de votre inscription')
                    ->setFrom('contact.afadec@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'user/registrationMail.html.twig',
                            array(
                                'prenom' => $user->getFirstname(),
                                'nom' => $user->getLastname(),
                                'id' => $user->getId(),
                                'url' => str_replace($routeName, 'activation', $actual_link),
                                'urlLogin' =>str_replace($routeName, 'login', $actual_link),
                                'confirmedByAdmin' => $user->getConfirmedByAdmin(),
                                'statut' => $user->getStatut(),
                            )
                        ),
                        'text/html'
                    )
                    /*
                     * If you also want to include a plaintext version of the message
                    ->addPart(
                        $this->renderView(
                            'Emails/registration.txt.twig',
                            array('name' => $name)
                        ),
                        'text/plain'
                    )
                    */
                ;
                $this->get('mailer')->send($message);


                return $this->redirectToRoute('registration', array('userId' => $user->getId()));
            }


        }

        return $this->render('user/add.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/registration/{userId}", name="registration")
     */
    public function confirmInscrAction(Request $request, $userId)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        if($user){
            return $this->render('user/registration.html.twig', ['user' => $user]);
        }else{
            return $this->redirectToRoute('homepage');
        }


    }

    /**
     * @Route("/inscriptionDiscipline", name="inscriptionDiscipline")
     */
    public function inscriptionDisciplineAction(Request $request)
    {
        /* @var $user User */
        $user = $this->getUser();
        if((($user->getStatut() !== 'Responsable' && $user->getStatut() !== 'Formateur') || !$user->getConfirmedByAdmin()) && !$this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            return $this->redirectToRoute('homepage');
        }

        /* @var $discRepo DisciplineRepository */
        $discRepo = $this->getDoctrine()->getRepository('AppBundle:Discipline');

        $disciplines = $discRepo->findAll();
        $discAccess = [];
        $discNoAccess = [];
        if($disciplines){
            /* @var $discipline Discipline */
            foreach ($disciplines as $discipline){
                if($discRepo->userHasAccessOrIsInscrit($user, $discipline)){
                    array_push($discAccess, $discipline);
                }else{
                    array_push($discNoAccess, $discipline);
                }
            }
        }

        return $this->render('user/myInscriptions.html.twig', array(
            'discAccess' => $discAccess,
            'discNoAccess' => $discNoAccess
        ));
    }

    /**
     * @Route("/updateDisInscrDatas_ajax", name="updateDisInscrDatas_ajax")
     */
    public function updateDisInscrDatasAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $noAction = false;

            $idUser = $request->request->get('idUser');
            $idDisc = $request->request->get('idDisc');
            $isInscr = $request->request->get('isInscr') == "true";

            /* @var $discipline Discipline */
            $discipline = $this->getDoctrine()->getRepository('AppBundle:Discipline')
                ->findOneBy(array('id' => $idDisc));

            /* @var $user User */
            $user = $this->getDoctrine()->getRepository('AppBundle:User')
                ->findOneBy(array('id' => $idUser));

            if($isInscr){
                /* @var $role Role */
                $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));
                $inscr = new Inscription_d();
                $inscr->setUser($user);
                $inscr->setDiscipline($discipline);
                $inscr->setDateInscription(new DateTime());
                $inscr->setRole($role);
                $em->persist($inscr);
            }else{
                $inscr = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')
                    ->findOneBy(array('user' => $user, 'discipline' => $discipline));
                if($inscr){
                    $em->remove($inscr);
                }else{
                    $noAction = true;
                }
            }

            $em->flush();

            return new JsonResponse(array(
                'action' => 'delete inscription of user to a discipline',
                'noAction' => $noAction
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/activation/{id}", name="activation")
     */
    public function activationAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $id));
        if($user){
            $user->setEnabled(true);

            $em->persist($user);
            $em->flush();

            return $this->render('user/activation.html.twig', ['user' => $user]);
        }else{
            return $this->redirectToRoute('homepage');
        }


    }
}
