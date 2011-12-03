<?php

namespace Drosera\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueUsername extends Constraint
{
    public $message = 'Zadané uživatelské jméno již existuje.';
    public $property;

    public function defaultOption()
    {
        return 'property';
    }

    public function requiredOptions()
    {
        return array('property');
    }

    public function validatedBy()
    {
        return 'drosera_user.validator.unique_username';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
