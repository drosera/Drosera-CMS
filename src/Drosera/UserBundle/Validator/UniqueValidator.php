<?php

namespace Drosera\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class UniqueValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }
    
    private function getClassRepository()
    {
        $className = $this->context->getCurrentClass();
        return $this->getEntityManager()->getRepository($className);
    }

    public function isValid($object, Constraint $constraint)
    {
        if (!$this->getClassRepository()->isUnique($object, $constraint->property)) {
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
