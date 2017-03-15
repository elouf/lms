<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', TextType::class)
            ->add('identifiant', TextType::class)
            ->add('mdp', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe')
                ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
                'choice_label' => 'nom',
                'multiple' => false
                ))
            ->add('submit', SubmitType::class)
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
            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('[AFADEC] Confirmation de votre inscription')
                ->setFrom('contact.afadec@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'user/registrationMail.html.twig',
                        array(
                            'prenom' => $user->getFirstname(),
                            'nom' => $user->getFirstname(),
                            'id' => $user->getId()
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
}
