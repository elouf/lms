<?php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class Evt_userAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nom', 'text')
            ->add('description', 'textarea')
            ->add('user', 'sonata_type_model')
            ->add('dateDebut', 'sonata_type_datetime_picker', array(
                'dp_side_by_side'       => true,
                'dp_use_current'        => false,
                'dp_use_seconds'        => false,
                'dp_collapse'           => true,
                'dp_calendar_weeks'     => false,
                'dp_view_mode'          => 'days'))
            ->add('dateFin', 'sonata_type_datetime_picker', array(
                'dp_side_by_side'       => true,
                'dp_use_current'        => false,
                'dp_use_seconds'        => false,
                'dp_collapse'           => true,
                'dp_calendar_weeks'     => false,
                'dp_view_mode'          => 'days'))
        ;

    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('user', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Evt_user',
                'choice_label' => 'nom',
            ))
            ->add('dateDebut', 'doctrine_orm_date_range', array(
                'field_type' => 'sonata_type_date_range_picker',
            ))
            ->add('dateFin', 'doctrine_orm_date_range', array(
                'field_type' => 'sonata_type_date_range_picker',
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('description')
            ->addIdentifier('user')
        ;
    }
}