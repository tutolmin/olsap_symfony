<?php

namespace App\Form;

use App\Entity\Addresses;
use Doctrine\ORM\EntityRepository;
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
                                                             ->where('p.address_id is NULL');
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
