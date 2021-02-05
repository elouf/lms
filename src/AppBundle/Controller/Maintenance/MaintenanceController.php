<?php

namespace AppBundle\Controller\Maintenance;

use AppBundle\Entity\Cours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/")
 */
class MaintenanceController extends Controller
{
    /**
     * Supprime tous les fichiers des cours qui ne sont plus utiles au cours
     * @Route("cours/maintenance", name="maintenanceByAllCourse", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function maintenanceByAllCoursesAction(Request $request)
    {
        $em = $this->getDoctrine();
        $title = date("Y-m-d") . "-log.txt";
        //vérifie que le dossier des logs est bien créer
        if (!$this->folder_exist($this->get('kernel')->getRootDir() . "/../web/log/")) {
            mkdir($this->get('kernel')->getRootDir() . "/../web/log/");
        }
        $dirFile = $this->get('kernel')->getRootDir() . "/../web/log/" . $title;
        $containsDirLog = "/log/" . $title;
        $txt = fopen($this->get('kernel')->getRootDir() . "/../web/log/" . $title, "w") or die("Impossible de créer des logs!");
        //récupère les répertoires présents
        $rootDir = realpath($this->get('kernel')->getRootDir() . "/../var/upload");
        $currentDirectory = scandir($rootDir);
        $i = 0;
        foreach ($currentDirectory as $directory) {
            // on récupère seulement le dossier
            if ($directory != "." && $directory != "..") {
                $dirContent = $rootDir . "/" . $directory;
                //vérifie que c'est un dossier si oui on le récupère et on vérifie le contenu
                if (is_dir($dirContent)) {
                    // Vérifie si ce cours existe si oui on vérifie récursivement le dossier
                    /**
                     * @var Cours $cours
                     */
                    $cours = $em->getRepository('AppBundle:Cours')->findOneBy(['id' => $directory]);
                    if ($cours) {
                        //identifie la ressource que l'on veux
                        $curDirectory = scandir($dirContent);
                        foreach ($curDirectory as $ressource) {
                            // ignore les chemins supérieur
                            if (is_dir($dirContent . "/" . $ressource)) {
                                if ($ressource != "." && $ressource != "..") {
                                    $this->recursiveDirectory($txt, $dirContent, $i, $ressource, false);
                                }
                            }
                            //on ignore les autres fichiers ou répertoire racine
                        }
                    } else {
                        //signifie qu'il n'y a pas de cours supprime le dossier est son contenu
                        $this->rrmdir($dirContent);
                        fwrite($txt, "[" . date("Y-m-d H:i:s") . "] Suppression du dossier $dirContent \n");
                    }
                }
            }
        }
        fclose($txt);
        return $this->render("maintenance/maintenances.html.twig", ['fileDownload' => $containsDirLog]);
    }


    /**
     * Vérifie si le dossier existe ou non
     * @param string $folder Chemin que l'on veux vérifié.
     * @return bool retourne si le dossier n'existe pas
     */
    function folder_exist($folder)
    {
        $path = realpath($folder);
        if ($path !== false and is_dir($path)) {
            return true;
        }
        return false;
    }

    /**
     * Fonction permettant de supprimer le dossier en fonction de la ressource ou d'un devoir, ect...
     * @param resource $txt - Fichier que l'on veux créer
     * @param string $dirContent - Contenu du répertoire
     * @param integer $count - Permet de savoir sur quel niveau on est, pour les logs
     * @param string $idRess - Id d'entité ou nom du dossier
     * @param bool $toCheck - Vérifie si on supprime ou non le dossier et permet de pouvoir faire le chemin :)
     * @param string|null $resContent - Permet de savoir si on utilise ou non une entité afin de l'identifier
     * @return void
     */
    private function recursiveDirectory($txt, $dirContent, $count, $idRess, $toCheck, $resContent = null)
    {
        $resContent = $this->knownContent($resContent);
        $em = $this->getDoctrine();
        // On veux le vrai chemin
        $localDir = realpath($dirContent . "/" . $idRess);
        //récupère le contenu du dossier quoi qu'il arrive
        $localDirContent = scandir($localDir);
        //On connais le chemin donc on s'amuse un peux avec
        if ($toCheck) {
            $resInfo = $em->getRepository("AppBundle:$resContent")->findOneBy(['id' => $idRess]);
            if (!$resInfo) {
                //si il n'y as pas de ressource il faut supprimer le dossier et son contenu afin de faire un cleanup
                fwrite($txt, "[" . date("Y-m-d H:i:s") . "] Suppression du dossier " . $localDir . "\n");
                $this->rrmdir($localDir);
            } else {
                if($resContent != "Lien"){
                    //fwrite($txt, "[" . date("Y-m-d H:i:s") . "] Lecture du dossier " . $localDir . "\n");
                    foreach ($localDirContent as $res) {
                        if ($res != "." && $res != "..") {
                            if (is_dir($localDir . "/" . $res)) {
                                $this->recursiveDirectory($txt, $localDir, $count + 1, $res, false, $idRess);
                            }
                        }
                    }
                }

            }
        } else {
            //On veux pas vérifier cette partie donc on continue dans le dossier suivant que l'on voudras continuer
            foreach ($localDirContent as $res) {
                // ignore les chemins supérieur
                if ($res != "." && $res != "..") {
                    if (is_dir($localDir . "/" . $res)) {
                        //fwrite($txt, "[" . date("Y-m-d H:i:s") . "] Lecture du dossier " . $localDir . "\n");
                        //on parcours les repertoire avec une variable en temporaire que l'on fait passé afin de connaitre
                        $this->recursiveDirectory($txt, $localDir, $count + 1, $res, true, $idRess);
                    }
                }
            }
        }
    }

    /**
     * Fonction permettant de supprimer un dossier
     * @param string $dir - Chemin que l'on souhaite supprimer
     * @return void
     */
    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    }else{
                        //unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            //rmdir($dir);
        }
    }

    /**
     * Methode permettant de convertir le nom du dossier en entités afin d'éviter les futures crash
     * @param string|null $resContent
     * @return string|null
     */
    private function knownContent($resContent)
    {
        switch (strtolower($resContent)) {
            case "copies":
                return "CopieFichier";
            case "corriges":
                return "CorrigeFichier";
            case "corrigetypes":
                return "DevoirCorrigeType";
            case "devoir":
                return "Devoir";
            case "sujets":
                return "DevoirSujet";
            case "lien":
                return "Lien";
            case "podcasts":
                return "Podcast";
            default:
                return null;
        }
    }
}
