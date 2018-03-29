<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_d;
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
use Symfony\Component\HttpFoundation\Request;

class InscriptionController extends Controller
{

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscriptionAction (Request $request)
    {
        date_default_timezone_set('Europe/Paris');

        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('prenom', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('email', EmailType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Numéro de téléphone',
                'required' => false,
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('mdp', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'required' => true,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe'),
                'options' => array('attr' => array('class' => 'col-sm-8'))
                ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
                'query_builder' => function (InstitutRepository $in) {
                    return $in->createQueryBuilder('i')
                        ->orderBy('i.nom', 'ASC')
                        ->where('i.actif = true');
                },
                'choice_label' => 'nom',
                'multiple' => false,
                'label_attr' => array('class' => 'col-sm-4')
                ))
            ->add('typeUser', ChoiceType::class, array(
                'choices'  => array(
                    'Étudiant' => 0,
                    'Formateur' => 1,
                    'Professeur stagiaire' => 2
                ),
                'label' => 'Vous êtes',
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('concours', ChoiceType::class, array(
                'choices'  => array(
                    '1er Degré' => 0,
                    '2nd Degré' => 1
                ),
                'label' => 'Concours préparé',
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('sectionEns', ChoiceType::class, array(
                'choices'  => array(
                    '1er Degré' => 0,
                    '2nd Degré' => 1
                ),
                'label' => "Section d'enseignement",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('matiereEtu', ChoiceType::class, array(
                'choices'  => array(
                    'Anglais' => 'Anglais',
                    'EMCC' => 'EMCC',
                    'Espagnol' => 'Espagnol',
                    'Histoire-Géographie' => 'HG',
                    'Lettres modernes' => 'Lettres',
                    'Mathématiques' => 'Maths',
                    'Sciences physiques et chimiques' => 'PhyChi',
                    'SVT' => 'SVT'
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('matiereForm', ChoiceType::class, array(
                'choices'  => array(
                    'Allemand' => 'Allemand',
                    'Anglais' => 'Anglais',
                    'Arts appliqués' => 'ArtsAppl',
                    'Arts plastiques' => 'ArtsPlast',
                    'Documentation' => 'Documentation',
                    'Economie et gestion' => 'EcoGest',
                    'Education musicale et chant choral' => 'EducMusChantChoral',
                    'EMCC' => 'EMCC',
                    'Espagnol' => 'Espagnol',
                    'Espagnol M2' => 'EspagnolM2',
                    'Histoire-Géographie' => 'HG',
                    'Lettres modernes' => 'Lettres',
                    'Mathématiques' => 'Maths',
                    'Philosophie' => 'Philosophie',
                    'Sciences économiques et sociales' => 'SES',
                    'Sciences industrielles - Génie' => 'SIgenie',
                    'STMS - Biotechnologies' => 'STMS',
                    'Sciences physiques et chimiques' => 'PhyChi',
                    'SVT' => 'SVT'
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('matiereProfStag', ChoiceType::class, array(
                'choices'  => array(
                    'Allemand' => 'Allemand',
                    'Arts appliqués' => 'ArtsAppl',
                    'Arts plastiques' => 'ArtsPlast',
                    'Documentation' => 'Documentation',
                    'Economie et gestion' => 'EcoGest',
                    'Education musicale et chant choral' => 'EducMusChantChoral',
                    'Espagnol' => 'EspagnolM2',
                    'Philosophie' => 'Philosophie',
                    'Sciences économiques et sociales' => 'SES',
                    'Sciences industrielles - Génie' => 'SIgenie',
                    'STMS - Biotechnologies' => 'STMS'
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
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

                $em->persist($user);


                $nomCoh = "";
                $role = "";
                if($data['typeUser'] == 0){
                    // Etudiant
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));
                    if($data['concours'] == 0){
                        // CRPE étudiant
                        $nomCoh = 'crpe';
                    }elseif($data['concours'] == 1){
                        // CAPES étudiant
                        $nomCoh = $data['matiereEtu'];
                    }
                }elseif($data['typeUser'] == 1){
                    // Formateur
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Enseignant'));
                    if($data['sectionEns'] == 0){
                        // CRPE formateur
                        $nomCoh = 'crpe';
                    }elseif($data['sectionEns'] == 1){
                        // CAPES étudiant
                        $nomCoh = $data['matiereForm'];
                    }
                }elseif($data['typeUser'] == 2){
                    // Prof stagiaire
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Stagiaire'));
                    $nomCoh = $data['matiereProfStag'];
                }
                $coh = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('nom' => $nomCoh));
                $inscr = new Inscription_coh();
                $inscr->setUser($user);
                $inscr->setCohorte($coh);
                $inscr->setDateInscription(new DateTime());
                $inscr->setRole($role);
                $em->persist($inscr);

                /*if($data['optionsDisc'] != '0'){
                    $disc = $em->getRepository('AppBundle:Discipline')->findOneBy(array('nom' => $data['optionsDisc']));
                    $inscrD = new Inscription_d();
                    $inscrD->setDiscipline($disc);
                    $inscrD->setDateInscription(new DateTime());
                    $inscrD->setRole($role);
                    $inscrD->setUser($user);
                    $em->persist($inscrD);

                }*/

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
                                'urlLogin' =>str_replace($routeName, 'login', $actual_link)
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
