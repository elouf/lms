<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cours;
use AppBundle\Entity\Section;
use AppBundle\Entity\UserStatCours;
use AppBundle\Entity\UserStatRessource;
use AppBundle\Repository\SectionRepository;
use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use DateTime;

class CoursController extends Controller
{
    /**
     * @Route("/courses", name="courses")
     */
    public function coursesAction (Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $cours = $repository->findAll();

        return $this->render('cours/list.html.twig', ['courses' => $cours]);
    }

    /**
     * @Route("/courses/disc/{id}", name="coursesForDisc")
     */
    public function coursesByDiscAction (Request $request, $id)
    {
        $repositoryD = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disc = $repositoryD->find($id);

        $repositoryC = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $courses = $repositoryC->findBy(array('discipline' => $disc));

        return $this->render('cours/one.html.twig', ['courses' => $courses]);
    }

    /**
     *
     * @Route("/cours/{id}/mode/{mode}", name="oneCours")
     */
    public function oneCoursAction (Request $request, $id, $mode)
    {
        date_default_timezone_set('Europe/Paris');
        ini_set('session.gc_maxlifetime', 21600);

        $isReferent = false;
        $user = $this->getUser();
        $statut = $user->getStatut();
        $isAdmin = $user->hasRole('ROLE_SUPER_ADMIN');

        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $discipline = $cours->getDiscipline();
        $repositoryC = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        $repoSess = $this->getDoctrine()->getRepository('AppBundle:Session');

        $allcourses = $repositoryC->findBy(array('discipline' => $discipline));
        $courses = array();
        foreach($allcourses as $coursFiltre){
            if($coursFiltre->getSession() == null || $isAdmin){
                array_push($courses, $coursFiltre);
            }else{
                $currentDate = new DateTime();
                $sess = $coursFiltre->getSession();
                if($repoSess->userIsInscrit($user, $sess) &&
                    $currentDate >= $sess->getDateDebut() &&
                    $currentDate <= $sess->getDateFin()){
                    array_push($courses, $coursFiltre);
                }
            }
        }

        $role = "";
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        $repoInscription_coh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
        $repoInscription_d = $this->getDoctrine()->getRepository('AppBundle:Inscription_d');
        $repoInscription_c = $this->getDoctrine()->getRepository('AppBundle:Inscription_c');
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                    $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                    if($inscrCoh){
                        $role = $inscrCoh->getRole()->getNom();
                        break;
                    }
                }
            }
        }
        if($role == ""){
            $inscrDis = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $discipline));
            if($inscrDis) {
                $role = $inscrDis->getRole()->getNom();
            }
        }

        $inscrC = $repoInscription_c->findOneBy(array('user' => $user, 'cours' => $cours));
        if($inscrC) {
            if($role == "" || $inscrC->getRole()->getNom() == "Referent") {
                $role = $inscrC->getRole()->getNom();
            }
        }
        // on corrige le statut du user. Si c'est un enseignant, il ne doit pas être en etu. Si ce n'est pas un admin, il ne doit pas être admin
        if( !($isAdmin || (($statut == 'Responsable' || $statut == 'Formateur') && $user->getConfirmedByAdmin())) ){
            if($role == "Enseignant"){
                $mode = 'ens';
            }else{
                $mode = 'etu';
            }
        }
        if($role == "Referent"){
            $isReferent = true;
        }

        $typeLiens = $this->getDoctrine()->getRepository('AppBundle:TypeLien')->findAll();
        $categorieLiens = $this->getDoctrine()->getRepository('AppBundle:CategorieLien')->findAll();

        $sections = $this->getDoctrine()->getRepository('AppBundle:Section')->findBy(array('cours' => $cours), array('position' => 'ASC'));

        // On commence par récupérer le contenu des sections du cours
        $datas = array();
        $repoZoneRessource = $this->getDoctrine()->getRepository('AppBundle:ZoneRessource');
        $repoRessource = $this->getDoctrine()->getRepository('AppBundle:Ressource');
        $repoDevoir = $this->getDoctrine()->getRepository('AppBundle:Devoir');
        $repoDevoirSujet = $this->getDoctrine()->getRepository('AppBundle:DevoirSujet');
        $repoDevoirCorrigeType = $this->getDoctrine()->getRepository('AppBundle:DevoirCorrigeType');
        $repoCopie = $this->getDoctrine()->getRepository('AppBundle:Copie');
        $repoCopieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier');
        $repoCorrige = $this->getDoctrine()->getRepository('AppBundle:Corrige');
        $repoCorrigeFichier = $this->getDoctrine()->getRepository('AppBundle:CorrigeFichier');
        $repoAssocGroupeLiens = $this->getDoctrine()->getRepository('AppBundle:AssocGroupeLiens');
        for($i=0; $i<count($sections); $i++){
            $datas[$i]["section"] = $sections[$i];

            $zones = $repoZoneRessource->findBy(array('section' => $sections[$i]), array('position' => 'ASC'));
            $datas[$i]["zones"]["containers"] = $zones;
            $datas[$i]["zones"]["content"] = array();
            $datas[$i]["zones"]["type"] = array();
            for($j=0; $j<count($zones); $j++){
                $zone = $datas[$i]["zones"]["containers"][$j];

                if($zone->getRessource() != null){
                    $ressource = $repoRessource->findOneBy(array('id' => $zone->getRessource()->getId()));
                    $ressType = $ressource->getType();
                    $datas[$i]["zones"]["type"][$j] = $ressType;

                    if($ressType == "lien"){
                        $datas[$i]["zones"]["type"][$j] = "lien";
                        $datas[$i]["zones"]["content"][$j] = $ressource;
                    }elseif($ressType == "forum"){
                        $datas[$i]["zones"]["type"][$j] = "forum";
                        $datas[$i]["zones"]["content"][$j] = $ressource;
                    }elseif($ressType == "chat"){
                        $datas[$i]["zones"]["type"][$j] = "chat";
                        $datas[$i]["zones"]["content"][$j] = $ressource;
                    }elseif($ressType == "devoir"){
                        $datas[$i]["zones"]["type"][$j] = "devoir";

                        $repositorySujet = $repoDevoirSujet->findBy(array('devoir' => $ressource), array('position' => 'ASC'));

                        $repositoryCorrigeType = $repoDevoirCorrigeType->findBy(array('devoir' => $ressource), array('position' => 'ASC'));

                        $datas[$i]["zones"]["content"][$j] = $ressource;
                        $datas[$i]["zones"]["sujet"][$j] = $repositorySujet;
                        $datas[$i]["zones"]["corrigeType"][$j] = "undefined";

                        if($repositoryCorrigeType) {
                            $datas[$i]["zones"]["corrigeType"][$j] = $repositoryCorrigeType;
                        }

                        // on a pas besoin des copies du user si on est en mode admin, par contre en etu, oui
                        if($mode == "admin"){

                        }elseif($mode == 'ens'){
                            // on compte le nombre de copies non corrigées
                            $datas[$i]["zones"]["copiesDeposes"][$j] = 0;
                            $datas[$i]["zones"]["corrigesDeposes"][$j] = 0;

                            $copies = $repoCopie->findBy(array('devoir' => $ressource));
                            for($u=0; $u<count($copies); $u++){
                                $copieFichier = $repoCopieFichier->findOneBy(array('copie' => $copies[$u]));
                                if($copieFichier){
                                    $datas[$i]["zones"]["copiesDeposes"][$j]++;
                                    $corrigeFichier = $repoCorrige->findOneBy(array('copie' => $copies[$u]));
                                    if($corrigeFichier){
                                        $datas[$i]["zones"]["corrigesDeposes"][$j]++;
                                    }
                                }
                            }
                        }else{
                            $datas[$i]["zones"]["copie"][$j] = "undefined";
                            $datas[$i]["zones"]["corrige"][$j] = "undefined";
                            $datas[$i]["zones"]["copieFichier"][$j] = "undefined";
                            $datas[$i]["zones"]["corrigeFichier"][$j] = "undefined";

                            $copie = $repoCopie->findOneBy(array('devoir' => $ressource, 'auteur' => $user));
                            if($copie){
                                $datas[$i]["zones"]["copie"][$j] = $copie;

                                $copieFichier = $repoCopieFichier->findOneBy(array('copie' => $copie));
                                if($copieFichier){
                                    $datas[$i]["zones"]["copieFichier"][$j] = $copieFichier;
                                }

                                $corrige = $repoCorrige->findOneBy(array('copie' => $copie));
                                if($corrige){
                                    $datas[$i]["zones"]["corrige"][$j] = $corrige;
                                    $corrigeFichier = $repoCorrigeFichier->findOneBy(array('corrige' => $corrige));
                                    $datas[$i]["zones"]["corrigeFichier"][$j] = $corrigeFichier;
                                }
                            }
                        }

                    }elseif($ressType == "groupe") {
                        $repositoryGaL = $repoAssocGroupeLiens->findBy(array('groupe' => $ressource), array('position' => 'ASC'));
                        $datas[$i]["zones"]["groupe"][$j] = $ressource;
                        $datas[$i]["zones"]["content"][$j] = $repositoryGaL;
                    }elseif($ressType == "libre"){
                            $datas[$i]["zones"]["content"][$j] = $ressource;
                    }else{
                        // on ne trouve pas le type de la ressource
                        $datas[$i]["zones"]["type"][$j] = "unknown";
                        $datas[$i]["zones"]["content"][$j] = $zone->getDescription();
                    }
                }else{
                    // Aucune ressource associée
                    $datas[$i]["zones"]["type"][$j] = "free";
                    $datas[$i]["zones"]["content"][$j] = $zone->getDescription();
                }
            }
        }

        // on récupère aussi tout le contenu du cours
        $cLiens = $this->getDoctrine()->getRepository('AppBundle:Lien')->findBy(array('cours' => $cours));
        $cForums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findBy(array('cours' => $cours));
        $cChats = $this->getDoctrine()->getRepository('AppBundle:Chat')->findBy(array('cours' => $cours));
        $cLibres = $this->getDoctrine()->getRepository('AppBundle:RessourceLibre')->findBy(array('cours' => $cours));

        $cGroupesEntity = $this->getDoctrine()->getRepository('AppBundle:GroupeLiens')->findBy(array('cours' => $cours));
        $cGroupes = array();
        for($i=0; $i<count($cGroupesEntity); $i++){
            $repositoryGaL = $repoAssocGroupeLiens->findBy(array('groupe' => $cGroupesEntity[$i]))
            ;
            $cGroupes[$i]['groupe'] = $cGroupesEntity[$i];
            $cGroupes[$i]['content'] = $repositoryGaL;
        }

        $cDevoirsEntity = $repoDevoir->findBy(array('cours' => $cours));
        $cDevoirs = array();
        for($i=0; $i<count($cDevoirsEntity); $i++){
            $repositorySujet = $repoDevoirSujet->findBy(array('devoir' => $cDevoirsEntity[$i]));
            $repositoryCorrigeType = $repoDevoirCorrigeType->findBy(array('devoir' => $cDevoirsEntity[$i]));

            $cDevoirs[$i]['content'] = $cDevoirsEntity[$i];
            $cDevoirs[$i]['sujets'] = $repositorySujet;
            $cDevoirs[$i]['corrigesType'] = $repositoryCorrigeType;
        }

        //Comme un accès aux documents du cours existe, on doit afficher l'info-bulle si certains n'ont pas été visités
        $docs = $this->getDoctrine()->getRepository('AppBundle:Document')->findByCours($cours, $user);
        $documents = array_merge($docs[0], $docs[1]);

        $nbNewDocs = 0;
        $repoStatsUsersDocs = $this->getDoctrine()->getRepository('AppBundle:StatsUsersDocs');
        foreach($documents as $doc){
            $stat = $repoStatsUsersDocs->findBy(array('user' => $user, 'document' => $doc));
            if(!$stat){
                $nbNewDocs++;
            }
        }
        if($mode == "admin"){
            return $this->render('cours/oneAdmin.html.twig',
                [
                    'cours' => $cours,
                    'zonesSections' => $datas,
                    'liens' => $cLiens,
                    'forums' => $cForums,
                    'chats' => $cChats,
                    'devoirs' => $cDevoirs,
                    'groupes' => $cGroupes,
                    'libres' => $cLibres,
                    'typeLiens' => $typeLiens,
                    'categorieLiens' => $categorieLiens,
                    'folderUpload' => $this->getParameter('upload_directory'),
                    'uploadSteps' => $this->getParameter('upload_steps'),
                    'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                    'uploadCourse' => $this->getParameter('upload_course'),
                    'nbNewDocs' => $nbNewDocs,
                    'courses' => $courses,
                    'users' => $users,
                    'isReferent' => $isReferent
                ]);
        }elseif($mode == "ens") {
            return $this->render('cours/one.html.twig', [
                'cours' => $cours,
                'zonesSections' => $datas,
                'mode' => 'ens',
                'folderUpload' => $this->getParameter('upload_directory'),
                'uploadSteps' => $this->getParameter('upload_steps'),
                'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                'uploadCourse' => $this->getParameter('upload_course'),
                'nbNewDocs' => $nbNewDocs,
                'courses' => $courses,
                'isReferent' => $isReferent
            ]);
        }else{
            return $this->render('cours/one.html.twig', [
                'cours' => $cours,
                'zonesSections' => $datas,
                'mode' => 'etu',
                'folderUpload' => $this->getParameter('upload_directory'),
                'uploadSteps' => $this->getParameter('upload_steps'),
                'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                'uploadCourse' => $this->getParameter('upload_course'),
                'nbNewDocs' => $nbNewDocs,
                'courses' => $courses,
                'isReferent' => $isReferent
            ]);
        }
    }

    /**
     * @Route("/dupliqCours/{id}", name="dupliqCours")
     */
    public function dupliqCoursAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $repositoryC = $this->getDoctrine()->getRepository('AppBundle:Cours');
        /* @var Cours $coursOrig */
        $coursOrig = $repositoryC->find($id);

        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, array(
                'data' => $coursOrig->getNom()
            ))
            ->add('description', CKEditorType::class, [
                'config_name' => 'my_simple_config',
                "attr" => ['class' => 'form-control'],
                'data' => $coursOrig->getDescription(),
                "required" => false
            ])
            ->add('accueil', CKEditorType::class, [
                'config_name' => 'my_simple_config',
                "attr" => ['class' => 'form-control'],
                'data' => $coursOrig->getAccueil(),
                "required" => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'multiple' => false
            ])
            ->add('imageOrig', HiddenType::class, [
                'data' => $coursOrig->getImageFilename()
            ])
            ->add('discipline', EntityType::class, array(
                'class' => 'AppBundle:Discipline',
                'choice_label' => 'nom',
                'multiple' => false,
                'data' => $coursOrig->getDiscipline()
            ))
            ->add('sections', EntityType::class, array(
                'class' => 'AppBundle:Section',
                'choices' => $coursOrig->getSections(),
                'multiple' => true,
                "choice_label" => "nom",
                'data' => $coursOrig->getSections(),
                "required" => false
            ))
            ->add('visible', CheckboxType::class, array(
                'data' => false,
                "required" => false
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Générer le nouveau cours',
                'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm()
        ;


        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $cours = new Cours();
            $cours->setNom($form['nom']->getData());
            $cours->setDescription($form['description']->getData());
            $cours->setAccueil($form['accueil']->getData());
            $cours->setDiscipline($form['discipline']->getData());
            $cours->setEnabled($form['visible']->getData());

            if($form['imageFile']->getData()){
                $cours->setImageFile($form['imageFile']->getData());
            }else{
                $cours->setImageFilename($form['imageOrig']->getData());
            }
            $cours->upload();

            $em->persist($cours);

            /* @var $sectionCopy Section */
            foreach ($form['sections']->getData() as $sectionCopy){
                $section = new Section();
                $section->setNom($sectionCopy->getNom());
                $section->setPosition($sectionCopy->getPosition());
                $section->setFaIcon($sectionCopy->getFaIcon());
                $section->setIsVisible($sectionCopy->getIsVisible());
                $section->setIsAccesConditionne($sectionCopy->getIsAccesConditionne());
                $section->setCours($cours);
                $em->persist($section);
            }

            $em->flush();

            return $this->redirectToRoute('oneCours', array('id' => $cours->getId(), 'mode' => 'admin'));
        }
        return $this->render('cours/dupliq.html.twig', ['form' => $form->createView(), 'coursModel' => $coursOrig]);
    }

    /**
     * @Route("/getNbZoneRess_ajax", name="getNbZoneRess_ajax")
     * @Method({"GET", "POST"})
     */
    public function getNbZoneRessAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('idItem');

            $ressource = $em->getRepository('AppBundle:Ressource')->findOneBy(array('id' => $id));
            $zones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('ressource' => $ressource));

            return new JsonResponse(array('action' =>'get nb zone avec cette ressource', 'nb' => count($zones)));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/supprItem_ajax", name="supprItem_ajax")
     * @Method({"GET", "POST"})
     */
    public function supprItemAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('idItem');
            $type = $request->request->get('typeItem');

            $entityRessourceName = "";
            if($type == "groupe"){
                $entityRessourceName = "GroupeLiens";
            }elseif($type == "devoir"){
                $entityRessourceName = "Devoir";
            }elseif($type == "lien"){
                $entityRessourceName = "Lien";
            }elseif($type == "libre"){
                $entityRessourceName = "RessourceLibre";
            }elseif($type == "forum"){
                $entityRessourceName = "Forum";
            }elseif($type == "chat"){
                $entityRessourceName = "Chat";
            }

            if($entityRessourceName == ""){
                return new JsonResponse(array(
                        'error' => true,
                        'entityRessourceName' => "non reconnu")
                );
            }else {
                $ressource = $em->getRepository('AppBundle:' . $entityRessourceName)->findOneBy(array('id' => $id));
                $em->remove($ressource);
                $em->flush();
                return new JsonResponse(array('action' =>'delete Zone', 'id' => $ressource->getId()));
            }
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/checkDirForUploadFile_ajax", name="checkDirForUploadFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function checkDirForUploadFileAjax (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $url = $request->request->get('url');

            if(!is_dir($url)) {
                if (!mkdir($url, 0777, true)) {
                    return new JsonResponse(array('error' =>'Echec de la création du dossier', 'url' => $url));
                }else{
                    return new JsonResponse(array('action' =>'Dossier créé', 'url' => $url));
                }
            }else{
                return new JsonResponse(array('action' =>'Dossier existe déjà', 'url' => $url));
            }

        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeActivationDocsCours_ajax", name="changeActivationDocsCours_ajax")
     */
    public function changeActivationDocsCoursAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));
            $cours->setDocsActivated($isVisible == "false");

            $em->persist($cours);
            $em->flush();

            return new JsonResponse(array('action' =>'change Visibility of documents', 'id' => $cours->getId(), 'isVisible' => $cours->getDocsActivated()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addUserStatRessource_ajax", name="addUserStatRessource_ajax")
     */
    public function addUserStatRessourceAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $ressId = $request->request->get('ressId');
            $ressource = $em->getRepository('AppBundle:Ressource')->findOneBy(array('id' => $ressId));
            $user = $this->getUser();

            $stat = new UserStatRessource();

            $stat->setDateAcces(new DateTime());
            $stat->setRessource($ressource);
            $stat->setUser($user);

            $em->persist($stat);

            $em->flush();

            return new JsonResponse(array('action' =>'add a user Stat for a ressource', '$ressId' => $ressource->getId(), 'user' => $user->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addUserStatCours_ajax", name="addUserStatCours_ajax")
     */
    public function addUserStatCoursAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $coursId = $request->request->get('ressId');
            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $coursId));
            $user = $this->getUser();

            $stat = new UserStatCours();

            $stat->setDateAcces(new DateTime());
            $stat->setCours($cours);
            $stat->setUser($user);

            $em->persist($stat);

            $em->flush();

            return new JsonResponse(array('action' =>'add a user Stat for a ressource', '$coursId' => $cours->getId(), 'user' => $user->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/updateIntituleSharedDocsCours_ajax", name="updateIntituleSharedDocsCours_ajax")
     */
    public function updateIntituleSharedDocsCoursAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $intitule = $request->request->get('input');
            $coursId = $request->request->get('id');
            /* @var $cours Cours */
            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $coursId));

            $cours->setIntituleSharedDocs($intitule);

            $em->flush();

            return new JsonResponse(array('action' =>'update IntituleSharedDocs', '$coursId' => $cours->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
