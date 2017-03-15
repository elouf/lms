<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction(Request $request)
    {
        return $this->render('pagesFixes/faq.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/concours", name="concours")
     */
    public function concoursAction(Request $request)
    {
        return $this->render('pagesFixes/concours.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/metierEnseignant", name="metierEnseignant")
     */
    public function metierEnseignantAction(Request $request)
    {
        return $this->render('pagesFixes/metierEnseignant.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/afadec", name="afadec")
     */
    public function afadecAction(Request $request)
    {
        return $this->render('pagesFixes/afadec.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }


}
