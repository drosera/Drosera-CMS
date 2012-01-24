<?php

namespace Drosera\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class CredentialType extends AbstractType
{
    protected $grantUrl;
    protected $revokeUrl;
    protected $withSuperadmin;

    function __construct($grantUrl, $revokeUrl, $withSuperadmin = false) {
        $this->grantUrl = $grantUrl;
        $this->revokeUrl = $revokeUrl;
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
                 
        $builder->add('grantUrl', 'hidden', array('data' => $this->grantUrl, 'property_path' => false));
        $builder->add('revokeUrl', 'hidden', array('data' => $this->revokeUrl, 'property_path' => false));
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