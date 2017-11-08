<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Cours;
use AppBundle\Entity\Image;
use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class CoursAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {

        // get the current Image instance
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('required' => false);
        if ($image && ($webPath = $image->getWebPath())) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request_stack')->getCurrentRequest()->getBasePath().'/'.$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img style="max-width:300px;" src="'.$fullPath.'" class="admin-preview" />';
        }

        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                ->add('nom', 'text')
                ->add('cout', 'text')
                ->add('description', CKEditorType::class, array(
                    'config_name' => 'my_simple_config'
                ))
                ->add('accueil', CKEditorType::class, array(
                    'config_name' => 'my_simple_config'
                ))
            ->end()
            ->with('Architecture', array('class' => 'col-md-6'))
                ->add('discipline', 'sonata_type_model')
                ->add('cohortes')
                ->add('session', 'sonata_type_model', array(
                    'required' => false))
                ->add('position', 'text')
                ->add('imageFile', 'file', $fileFieldOptions)
            ->end();
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('discipline', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Discipline',
                'choice_label' => 'nom',
            ))
            ->add('cohortes', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Cohorte',
                'choice_label' => 'nom',
            ))
            ->add('session', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Session',
                'choice_label' => 'nom',
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('description')
            ->add('discipline', null, array(
                'label' => 'Discipline'
            ))
            ->add('session', null, array(
                'label' => 'Session'
            ))
            ->add('position')
        ;

    }

    public function prePersist($cours)
    {
        $this->manageFileUpload($cours);
    }

    public function preUpdate($cours)
    {
        $this->manageFileUpload($cours);
    }

    private function manageFileUpload(Cours $cours)
    {
        if ($cours->getImageFile()) {
            $cours->upload();
            $cours->refreshUpdated();
        }
    }
}