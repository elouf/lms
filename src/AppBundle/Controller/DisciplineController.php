<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discipline;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DisciplineController extends Controller
{

    /**
     * @Route("/myCourses", name="myCourses")
     */
    public function myCoursesAction (Request $request)
    {
        $repositoryC = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repository->findAll();

        $courses = array();
        for($i=0; $i<count($disciplines); $i++){
            $courses[$i]["discipline"] = $disciplines[$i];
            $courses[$i]["courses"] = $repositoryC->getByDisc($disciplines[$i]);
        }

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
}
