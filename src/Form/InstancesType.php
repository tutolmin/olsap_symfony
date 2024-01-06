<?php

namespace App\Form;

use App\Entity\Instances;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class InstancesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('created_at')
//            ->add('name')
            ->add('instance_type')
            ->add('number', IntegerType::class, ['mapped' => false])
//            ->add('envs')
//            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instances::class,
        ]);
    }
}
