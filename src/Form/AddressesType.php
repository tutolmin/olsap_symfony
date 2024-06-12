<?php

namespace App\Form;

use App\Entity\Addresses;
//use App\Entity\Ports;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ip')
            ->add('mac')
            ->add('port', 
                      'entity', 
                       array('class' => 'Ports',
                             'query_builder' => function(EntityRepository $er) {
                                                   return $er->createQueryBuilder('p')
                                                             ->where('p_address_id is NULL');
                             }))
            ->add('instance')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresses::class,
        ]);
    }
}
