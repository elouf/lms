<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discipline;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DisciplineController extends Controller
{

    /**
     * @Route("/myCourses", name="myCourses")
     */
    public function myCoursesAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repositoryCours = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $repositoryCoh = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        $repositoryDisc = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repositoryDisc->findAll();

        // Par défaut, admin : toutes les disciplines
        $disciplinesArray2Consider = $disciplines;

        $coursesIndiv = array();

        // si ce n'est pas l'admin, on fait le tri
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            // on créé un tableau qui contient les disciplines auxquelles l' user est inscrit par cohorte
            $repositoryInscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
            $inscrsCoh = $repositoryInscrCoh->findBy(array('user'=> $this->getUser()));
            $discInscrCoh = array();
            foreach($inscrsCoh as $inscrCoh){
                $cohorte = $repositoryCoh->find($inscrCoh->getCohorte());
                foreach($cohorte->getDisciplines() as $disc){
                    if(!in_array($disc, $discInscrCoh)){
                        array_push($discInscrCoh, $disc);
                    }
                }
            }

            $disciplinesArray2Consider = $discInscrCoh;

            // on ajoute les disciplines auxquelles le user est inscrit directement
            $repositoryInscrD = $this->getDoctrine()->getRepository('AppBundle:Inscription_d');
            $inscrsD = $repositoryInscrD->findBy(array('user'=> $this->getUser()));
            foreach($inscrsD as $inscrD){
                if(!in_array($inscrD->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($disciplinesArray2Consider, $inscrD->getDiscipline());
                }
            }

            // enfin, on ajoute les cours auxquels l'utilisateur est inscrit individuellement (du coup une portion de discipline)
            $repositoryInscrC = $this->getDoctrine()->getRepository('AppBundle:Inscription_c');
            $inscrsC = $repositoryInscrC->findBy(array('user'=> $this->getUser()));
            foreach($inscrsC as $inscrC){
                if(!in_array($inscrC->getCours()->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($coursesIndiv, $inscrC->getCours());
                }
            }
        }


        $courses = array();

        // on construit le tableau des disciplines/cours complètes
        for($i=0; $i<count($disciplinesArray2Consider); $i++){
            $courses[$i]["discipline"] = $disciplinesArray2Consider[$i];
            $courses[$i]["courses"] = $repositoryCours->findBy(array('discipline' => $disciplinesArray2Consider[$i]));
        }
        // on lui ajoute les cours individuels (avec leurs disciplines
        for($j=0; $j<count($coursesIndiv); $j++){
            $discExists = false;
            for($k=0; $k<count($courses); $k++){
                if($courses[$k]["discipline"] == $coursesIndiv[$j]->getDiscipline()){
                    array_push($courses[$k]["courses"], $coursesIndiv[$j]);
                    $discExists = true;
                }
            }
            if(!$discExists){
                $idx = count($courses);
                $courses[$idx]["discipline"] = $coursesIndiv[$j]->getDiscipline();
                $courses[$idx]["courses"] = array($coursesIndiv[$j]);
            }
        }
        dump($courses);
        return $this->render('discipline/myCourses.html.twig', ['courses' => $courses]);
    }

    /**
     * @Route("/discs", name="disciplines")
     */
    public function disciplinesAction (Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repository->findAll();

        return $this->render('discipline/list.html.twig', ['disciplines' => $disciplines]);
    }

    /**
     * @Route("/disc/{id}", name="oneDiscipline")
     */
    public function oneDisciplineAction (Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $discipline = $repository->find($id);

        $courses = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $discipline));

        return $this->render('discipline/one.html.twig', ['discipline' => $discipline, 'courses' => $courses]);
    }

    /**
     * @Route("/changeActivationDocsDisc_ajax", name="changeActivationDocsDisc_ajax")
     */
    public function changeActivationDocsDiscAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $disc = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));
            $disc->setDocsActivated($isVisible == "false");

            $em->persist($disc);
            $em->flush();

            return new JsonResponse(array('action' =>'change Visibility of documents', 'id' => $disc->getId(), 'isVisible' => $disc->getDocsActivated()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
