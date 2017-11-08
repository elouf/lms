<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Session;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class InscrCohAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-12'))
                ->add('user', 'sonata_type_model')
                ->add('role', 'sonata_type_model')
                ->add('cohorte', 'sonata_type_model')
                ->add('dateInscription', 'sonata_type_datetime_picker', array(
                    'dp_side_by_side'       => true,
                    'dp_use_current'        => false,
                    'dp_use_seconds'        => false,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days'))
            ->end();
    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\User',
                'choice_label' => 'email',
            ))
            ->add('cohorte', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Cohorte',
                'choice_label' => 'nom',
            ))
            ->add('role', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Role',
                'choice_label' => 'nom',
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user.firstname', null, array(
                'label' => 'Prénom'
            ))
            ->add('user.lastname', null, array(
                'label' => 'Nom'
            ))
            ->add('user', null, array(
                'label' => 'Email'
            ))
            ->add('cohorte', null, array(
                'label' => 'Cohorte'
            ))
            ->add('role', null, array(
                'label' => 'Role'
            ))
        ;

    }
}