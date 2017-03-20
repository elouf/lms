<?php

namespace AppBundle\Controller;

use DateTime;
use AppBundle\Entity\Inscription_coh;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('prenom', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('email', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('identifiant', TextType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('mdp', PasswordType::class, array(
                'label' => 'Mot de passe',
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
                ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
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
                    'Anglais' => 0,
                    'EMCCFOADM1' => 2,
                    'Espagnol' => 3,
                    'Histoire-Géographie' => 4,
                    'Lettres modernes' => 5,
                    'Mathématiques' => 6,
                    'Sciences physiques et chimiques' => 7,
                    'SVT' => 8
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('matiereForm', ChoiceType::class, array(
                'choices'  => array(
                    'Allemand' => 0,
                    'Anglais' => 1,
                    'Arts appliqués' => 2,
                    'Arts plastiques' => 3,
                    'Documentation' => 4,
                    'Economie et gestion' => 5,
                    'Education musicale et chant choral' => 6,
                    'EMCCFOADM1' => 7,
                    'Espagnol' => 8,
                    'Histoire-Géographie' => 9,
                    'Lettres modernes' => 10,
                    'Mathématiques' => 11,
                    'Philosophie' => 12,
                    'Sciences économiques et sociales' => 13,
                    'Sciences industrielles - Génie' => 14,
                    'STMS - Biotechnologies' => 15,
                    'Sciences physiques et chimiques' => 16,
                    'SVT' => 17
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('matiereProfStag', ChoiceType::class, array(
                'choices'  => array(
                    'Allemand' => 0,
                    'Arts plastiques' => 1,
                    'Arts appliqués' => 2,
                    'Documentation' => 3,
                    'Economie et gestion' => 4,
                    'Education musicale et chant choral' => 5,
                    'Espagnol' => 6,
                    'Philosophie' => 7,
                    'Sciences économiques et sociales' => 8,
                    'Sciences industrielles - Génie' => 9,
                    'STMS - Biotechnologies' => 10
                ),
                'label' => "Matière",
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('options', ChoiceType::class, array(
                'choices'  => array(
                    'Anglais niveau B2 (gratuit)' => 0,
                    'Langues Tell me More (payant)' => 1
                ),
                'label' => "Options",
                'label_attr' => array('class' => 'col-sm-4'),
                'multiple' => true,
                'expanded' => true
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider mon inscription'
            ))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $user = new User();

            $user->setFirstname($data['prenom']);
            $user->setLastname($data['nom']);
            $user->setEmail($data['email']);
            $user->setUsername($data['identifiant']);
            $user->setPlainPassword($data['mdp']);
            $user->setInstitut($data['institut']);

            $em = $this->getDoctrine()->getManager();
            //$em->persist($user);


            if($data['typeUser'] == 0){
                // Etudiant
                if($data['concours'] == 0){
                    // CRPE étudiant
                    $coh = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('nom' => 'crpe'));
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));
                    $inscr = new Inscription_coh();
                    $inscr->setUser($user);
                    $inscr->setCohorte($coh);
                    $inscr->setDateInscription(new DateTime());
                    $inscr->setRole($role);
                    //$em->persist($inscr);
                }elseif($data['concours'] == 1){
                    // CAPES étudiant

                }
            }elseif($data['typeUser'] == 1){
                // Formateur
                if($data['sectionEns'] == 0){
                    // CRPE formateur
                    $coh = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('nom' => 'crpe'));
                    $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Stagiaire'));
                    $inscr = new Inscription_coh();
                    $inscr->setUser($user);
                    $inscr->setCohorte($coh);
                    $inscr->setDateInscription(new DateTime());
                    $inscr->setRole($role);
                    //$em->persist($inscr);
                }elseif($data['sectionEns'] == 1){
                    // CAPES étudiant

                }
            }elseif($data['typeUser'] == 2){
                // Prof stagiaire

            }


            $em->flush();

            dump($data['typeUser']);

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
            //$this->get('mailer')->send($message);


            return $this->redirectToRoute('registration', array('userId' => $user->getId()));
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
