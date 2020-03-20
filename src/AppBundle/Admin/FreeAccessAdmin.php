<?php
namespace AppBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class FreeAccessAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('createdAt', 'sonata_type_datetime_picker', array(
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
            ->add('createdAt')
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('createdAt')
        ;
    }
}