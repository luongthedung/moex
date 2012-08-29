<?php

namespace Moex\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MeOrdersType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('phone')
            ->add('orderName')
            ->add('orderInfo', 'textarea', array('required' => false))
            ->add('orderFrom', 'text', array('attr' => array('readonly' => 'readonly')))
            ->add('orderTo', 'text', array('attr' => array('readonly' => 'readonly')))
            ->add('price', 'text', array('attr' => array('readonly' => 'readonly')))
            ->add('lat', 'text', array('attr' => array('readonly' => 'readonly')))
            ->add('lng', 'text', array('attr' => array('readonly' => 'readonly')))
        ;
    }

    public function getName()
    {
        return 'moex_corebundle_meorderstype';
    }
}
