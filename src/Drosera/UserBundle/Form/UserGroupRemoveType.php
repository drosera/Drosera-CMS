<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class UserGroupRemoveType extends AbstractType
{
    protected $withSuperadmin;

    function __construct($withSuperadmin = false) {
        $this->withSuperadmin = $withSuperadmin;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('action', 'choice', array(
            'label' => '',
            'choices' => array('1' => 'odstranit spolu se skupinou.', '2' => 'přesunout do uživatelské skupiny')
        ));
        
        $withSuperadmin = $this->withSuperadmin;
        $builder->add('user_group', 'entity', array(
            'class' => 'DroseraUserBundle:UserGroup',
            'query_builder' => function(EntityRepository $er) use ($withSuperadmin) {
                return $er->getAll(false, $withSuperadmin, true);
            },
        ));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array();
    }   

    public function getName()
    {
        return 'drosera_user_user_group_remove';
    }
}