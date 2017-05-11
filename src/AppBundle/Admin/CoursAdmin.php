<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;

class CoursAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                ->add('nom', 'text')
                ->add('cout', 'text')
                ->add('imgFilePath', 'text')
                ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')))
                ->add('accueil', 'textarea', array('attr' => array('class' => 'ckeditor')))
            ->end()
            ->with('Architecture', array('class' => 'col-md-6'))
                ->add('discipline', 'sonata_type_model')
                ->add('cohortes')
                ->add('session', 'sonata_type_model', array(
                    'required' => false))
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
            ->addIdentifier('discipline')
            ->add('session')
        ;

    }
}