<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username');
        $builder->add('password');
    }

    public function getName()
    {
        return 'login';
    }
}