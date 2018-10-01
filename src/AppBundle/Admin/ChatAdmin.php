<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ChatAdmin extends AbstractAdmin
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
                ->add('administrateurs')
            ->end()
            ->with('Paramètres', array('class' => 'col-md-6'))
                ->add('cours', 'sonata_type_model')
                ->add('enabled', null, array(
                    'label' => 'Activé'
                ))
            ->end()
        ;
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('administrateurs')
            ->add('cours', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Evt_cours',
                'choice_label' => 'nom',
            ))
            ->add('enabled', null, array(
                'label' => 'Activé'
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('description')
            ->add('administrateurs')
            ->add('cours', null, array(
                'label' => 'Cours'
            ))
            ->add('enabled', null, array(
                'label' => 'Activé'
            ))
        ;
    }
}