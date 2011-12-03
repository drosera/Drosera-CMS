<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityRepository;


class UserType extends AbstractType
{
    protected $loggedUser;
    protected $validationGroups;

    function __construct($validationGroups, UserInterface $loggedUser) {
        $this->loggedUser = $loggedUser;
        $this->validationGroups = $validationGroups;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username', null, array('label' => 'Login'));
        $builder->add('degree_front', null, array('label' => 'Titul před'));
        $builder->add('firstname', null, array('label' => 'Jméno'));
        $builder->add('lastname', null, array('label' => 'Příjmení'));
        $builder->add('degree_behind', null, array('label' => 'Titul za'));
        $builder->add('email', 'email', array('label' => 'E-mail'));
        $builder->add('telephone', null, array('label' => 'Telefon'));
        $builder->add('plainPassword', 'password', array('label' => 'Heslo', 'required' => false)); 
        $builder->add('passwordConfirm', 'password', array('label' => 'Potvrzení hesla', 'required' => false));
        //$builder->add('user_group', null, array('label' => 'Uživatelská skupina'));
        
        $loggedUser = $this->loggedUser;
        $builder->add('user_group', 'entity', array(
            'class' => 'DroseraUserBundle:UserGroup',
            'query_builder' => function(EntityRepository $er) use ($loggedUser) {
                return $er->getValid($loggedUser, true);
            },
        ));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Drosera\UserBundle\Entity\User',
            'validation_groups' => $this->validationGroups,
        );
    }   

    public function getName()
    {
        return 'drosera_user_user';
    }
}