<?php
namespace AppBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class SessionAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Informations', array('class' => 'col-md-6'))
                ->add('nom', 'text')
                ->add('accessOnlyForAdmin')
                /*->add('description', CKEditorType::class, array(
                    'config_name' => 'my_simple_config'
                ))*/
                ->add('messageAlerte', CKEditorType::class, array(
                    'config_name' => 'my_simple_config',
                    'help' => "Message visible avant la date de début, et tant que l'utilisateur ne s'est pas insrit"
                ))
                ->add('messageFinSession', CKEditorType::class, array(
                    'config_name' => 'my_simple_config',
                    'help' => "Message visible seulement par ceux qui ne se sont pas inscrits après le date de fin d'alerte"
                ))
            ->end()
            ->with('Timing', array('class' => 'col-md-6'))
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
                ->add('dateDebutAlerte', 'sonata_type_datetime_picker', array(
                    'dp_side_by_side'       => true,
                    'dp_use_current'        => false,
                    'dp_use_seconds'        => false,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days'))
                ->add('dateFinAlerte', 'sonata_type_datetime_picker', array(
                    'dp_side_by_side'       => true,
                    'dp_use_current'        => false,
                    'dp_use_seconds'        => false,
                    'dp_collapse'           => true,
                    'dp_calendar_weeks'     => false,
                    'dp_view_mode'          => 'days'))
            ->end();
        ;

    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('accessOnlyForAdmin')
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nom')
            ->add('accessOnlyForAdmin')
            ->add('messageAlerte')
            ->add('messageFinSession')
        ;
    }
}