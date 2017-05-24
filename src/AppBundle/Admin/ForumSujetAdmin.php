<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;

class ForumSujetAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                ->add('titre', 'text')
                ->add('ouvert')
                ->add('epingle')
            ->end()
            ->with('Architecture', array('class' => 'col-md-6'))
                ->add('forum', 'sonata_type_model', array(
                    'required' => true))
            ->end();
        ;
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titre')
            ->add('ouvert')
            ->add('epingle')
            ->add('forum', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Forum',
                'choice_label' => 'nom',
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('titre')
            ->add('ouvert')
            ->add('epingle')
            ->add('forum')
        ;
    }
}