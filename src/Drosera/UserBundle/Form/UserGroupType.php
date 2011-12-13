<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserGroupType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', null, array('label' => 'Název'));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Drosera\UserBundle\Entity\UserGroup',
        );
    }   

    public function getName()
    {
        return 'drosera_user_user_group';
    }
}