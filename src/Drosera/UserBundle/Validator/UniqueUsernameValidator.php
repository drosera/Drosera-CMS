<?php

namespace Drosera\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drosera\UserBundle\Manager\UserManager;

class UniqueUsernameValidator extends ConstraintValidator
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserManager()
    {
        return $this->userManager;
    }

    public function isValid($value, Constraint $constraint)
    {
        if (!$this->getUserManager()->validateUniqueUsername($value)) {
            $property_path = $this->context->getPropertyPath() . '.username';
            $this->context->setPropertyPath($property_path);
            $this->context->addViolation($constraint->message, array(), null);
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}
