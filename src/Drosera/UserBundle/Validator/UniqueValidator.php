<?php

namespace Drosera\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drosera\UserBundle\Manager\UserManager;

class UniqueValidator extends ConstraintValidator
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

    public function isValid($object, Constraint $constraint)
    {
        if (!$this->getUserManager()->validateUnique($object, $constraint)) {
            $property_path = $this->context->getPropertyPath() .'.'. $constraint->property;
            $this->context->setPropertyPath($property_path);
            $this->context->addViolation($constraint->message, array('%property%' => $constraint->property), null);
            $this->setMessage($constraint->message, array(
                '%property%' => $constraint->property,
            ));
            return false;
        }

        return true;
    }
}
