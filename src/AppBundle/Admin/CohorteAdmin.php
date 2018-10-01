<?php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CohorteAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Champs', array('class' => 'col-md-6'))
                ->add('nom', 'text')
            ->end()
            ->with('Architecture', array('class' => 'col-md-6'))
                ->add('disciplines')
                ->add('cours')
            ->end();
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('disciplines', null, array(
                'label' => 'Disciplines'
            ))
            ->add('cours', null, array(
                'label' => 'Cours'
            ))
        ;
    }
}