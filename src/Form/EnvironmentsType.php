<?php

namespace App\Form;

use App\Entity\Environments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EnvironmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('started_at')
//            ->add('valid')
            ->add('task')
            ->add('session')
            ->add('number', IntegerType::class, ['mapped' => false])
//            ->add('instance')
//            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Environments::class,
        ]);
    }
}
