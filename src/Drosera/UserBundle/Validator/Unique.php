<?php

namespace Drosera\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Unique extends Constraint
{
    public $message = 'Hodnota %property% již existuje.';
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
        return 'drosera_user.validator.unique';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
