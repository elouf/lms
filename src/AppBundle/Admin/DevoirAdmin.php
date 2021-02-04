<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class DevoirAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                ->add('nom', 'text')
                ->add('description', CKEditorType::class, array(
                        'config_name' => 'my_simple_config'
                    ))
            ->end()
            ->with('Paramètres', array('class' => 'col-md-6'))
                ->add('cours', 'sonata_type_model')
                ->add('dateDebut', 'sonata_type_datetime_picker', array(
                    'required'              => false,
                    'dp_side_by_side'       => true,
                    'dp_use_current'        => false,
                    'dp_use_seconds'        => false,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days'))
                ->add('dateFin', 'sonata_type_datetime_picker', array(
                    'required'              => false,
                    'dp_side_by_side'       => true,
                    'dp_use_current'        => false,
                    'dp_use_seconds'        => false,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days'))
            ->end()
        ;
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('cours', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Evt_cours',
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
            ->add('cours', null, array(
                'label' => 'Cours'
            ))
        ;
    }
}