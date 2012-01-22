<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class CredentialType extends AbstractType
{
    protected $withSuperadmin;

    function __construct($withSuperadmin = false) {
        $this->withSuperadmin = $withSuperadmin;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {        
        $withSuperadmin = $this->withSuperadmin;
        $builder->add('user_group', 'entity', array(
            'label'     => 'Uživatelská skupina',
            'class' => 'DroseraUserBundle:UserGroup',
            'query_builder' => function(EntityRepository $er) use ($withSuperadmin) {
                return $er->getAllExceptOne(3, false, $withSuperadmin, true);
            },
        ));
            
        $builder->add('user_view', 'checkbox', array(
            'label'     => 'Zobrazit',
            'required'  => false,
        ));
        
        $builder->add('user_edit', 'checkbox', array(
            'label'     => 'Upravit',
            'required'  => false,
        ));
        
        $builder->add('user_create', 'checkbox', array(
            'label'     => 'Vytvořit',
            'required'  => false,
        ));
        
        $builder->add('user_delete', 'checkbox', array(
            'label'     => 'Odstranit',
            'required'  => false,
        ));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array();
    }   

    public function getName()
    {
        return 'drosera_user_credential';
    }
}