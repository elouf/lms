<?php


namespace AppBundle\Service;

use AppBundle\Entity\RessourceH5P;
use AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\Core\H5PIntegration;
use Emmedy\H5PBundle\Core\H5POptions;
use H5PCore;

class RessourceH5PService
{
    protected $em;
    protected $h5pIntegrationContent;
    protected $H5PCore;
    protected $H5POptions;

    /**
     * @param EntityManager $em
     * @param H5PIntegration $h5pIntegrationContent
     * @param H5PCore $H5PCore
     * @param H5POptions $H5POptions
     */
    public function __construct(EntityManager $em, H5PIntegration $h5pIntegrationContent, H5PCore $H5PCore, H5POptions $H5POptions)
    {
        $this->em = $em;
        $this->h5pIntegrationContent = $h5pIntegrationContent;
        $this->H5PCore = $H5PCore;
        $this->H5POptions = $H5POptions;
    }

    /**
     * Récupère les ressources et renvoie si il y a un H5P ou pas
     * @param array $ressources - Array Contenant toute les ressources
     * @return array - Retourne un array contenant ou non des modules H5P
     */
    public function getRessourceContent($ressources){
        $files = "";
        $content= "";
        //Appelle des fonctions de H5P pour l'affichage
        $h5pIntegration = $this->h5pIntegrationContent->getGenericH5PIntegrationSettings();
        $h5pcontent = false;
        //modifie le tableau de ressources pour rajouter les ressources de H5P (fichier ect.... )
        /* @var \AppBundle\Entity\Ressource $ressource */
        foreach ($ressources as $ressource) {
            //recupère toute les ressources
            //tableau à créer en ajoutant tous ce qui suis
            if ($ressource->getH5p() != null) {
                //defini du contenu H5P
                $h5pcontent = true;
                $content = $ressource->getH5p();
                $contentIdStr = 'cid-' . $content->getId();
                $h5pIntegration['contents'][$contentIdStr] = $this->h5pIntegrationContent->getH5PContentIntegrationSettings($content);
                $preloaded_dependencies = $this->H5PCore->loadContentDependencies($content->getId(), 'preloaded');
                $files = $this->H5PCore->getDependenciesFiles($preloaded_dependencies, $this->H5POptions->getRelativeH5PPath());
                if ($content->getLibrary()->isFrame()) {
                    $jsFilePaths = array_map(function ($asset) {
                        return $asset->path;
                    }, $files['scripts']);
                    $cssFilePaths = array_map(function ($asset) {
                        return $asset->path;
                    }, $files['styles']);
                    $coreAssets = $this->h5pIntegrationContent->getCoreAssets();

                    $h5pIntegration['core']['scripts'] = $coreAssets['scripts'];
                    $h5pIntegration['core']['styles'] = $coreAssets['styles'];
                    $h5pIntegration['contents'][$contentIdStr]['scripts'] = $jsFilePaths;
                    $h5pIntegration['contents'][$contentIdStr]['styles'] = $cssFilePaths;
                }
            }
        }
        return ['h5pIntegration' => $h5pIntegration, 'H5PContent' => $h5pcontent, 'Files' =>$files, 'H5PFrameIntegration' => $content];
    }
}