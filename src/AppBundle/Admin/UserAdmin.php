<?php
namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{
    // EDIT and CREATE
    protected function configureFormFields(FormMapper $formMapper)
    {

        $passwordoptions = array(
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'translation_domain' => 'FOSUserBundle',
            'invalid_message' => 'fos_user.password.mismatch',
        );

        $this->record_id = $this->request->get($this->getIdParameter());
        if (!empty($this->record_id)) {
            $passwordoptions['required'] = false;
        } else {
            $passwordoptions['required'] = true;
        }

        $formMapper
            ->add('email', 'text')
            ->add('plainPassword', 'repeated', $passwordoptions)
            ->add('enabled')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('statut',
                'choice',
                array('choices' => User::getStatuts()))
            ->add('institut', 'sonata_type_model')
        ;

    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
            ->add('enabled')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('statut')
            ->add('institut', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Institut',
                'choice_label' => 'nom',
            ))
        ;
    }

    // VIEW ALL IN TABLE
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('enabled')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('institut')
            ->add('statut')
        ;
    }

    public function prePersist($object) {
        parent::prePersist($object);
        $this->updateUser($object);
    }

    public function preUpdate($object) {
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    public function updateUser(\AppBundle\Entity\User $u) {
        $um = $this->getConfigurationPool()->getContainer()->get('fos_user.user_manager');
        $um->updateUser($u, false);
    }
}