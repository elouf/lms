<?php
namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{
    public function getExportFormats()
    {
        return ['xls'];
    }
    public function getExportFields()
    {
        return array(
            'id' => 'id',
            'Email' => 'email',
            'Nom' => 'lastname',
            'Prénom' => 'firstname',
            'Actif' => 'enabled',
            'Statut' => 'statut',
            'Institut' => 'institut.nom',
            'Date de création' => 'createdAt',
            'Dernière connexion' => 'lastLogin',
            'Téléphone' => 'phone',
        );
    }

    public function getDataSourceIterator()
    {
        $datasourceit = parent::getDataSourceIterator();
        $datasourceit->setDateTimeFormat('Y.m.d'); //change this to suit your needs
        return $datasourceit;
    }

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
            ->add('canEditForumsMsgs')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('statut',
                'choice',
                array('choices' => User::getStatuts()))
            ->add('confirmedByAdmin', null, array(
                'label' => "Statut confirmé par l'admin"
            ))
            ->add('validInscriptionFormateurEngagement', null, array(
                'label' => "Engagement de Formateur validé"
            ))
            ->add('institut', 'sonata_type_model')
        ;

    }

    //FILTERS
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
            ->add('enabled')
            ->add('canEditForumsMsgs')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('statut')
            ->add('confirmedByAdmin', null, array(
                'label' => "Statut confirmé par l'admin"
            ))
            ->add('validInscriptionFormateurEngagement', null, array(
                'label' => "Engagement de Formateur validé"
            ))
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
            ->add('canEditForumsMsgs')
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('institut')
            ->add('statut')
            ->add('confirmedByAdmin', null, array(
                'label' => "Statut confirmé par l'admin"
            ))
            ->add('validInscriptionFormateurEngagement', null, array(
                'label' => "Engagement de Formateur validé"
            ))
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