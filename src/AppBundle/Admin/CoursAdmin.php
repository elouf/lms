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
        $subject = $this->getSubject();

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
            ->end();


        if ($subject instanceof Session) {
            $formMapper
                ->with('Session', array('class' => 'col-md-6'))
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
        }


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
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $subject = $this->getSubject();
        $isSession = $subject instanceof Session;

        $listMapper
            ->addIdentifier('nom')
            ->add('description')
            ->addIdentifier('discipline')
        ;

    }
}