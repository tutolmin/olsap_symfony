<?php

namespace App\Form;

use App\Entity\Addresses;
//use Doctrine\ORM\EntityRepository;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use App\Entity\Ports;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressesType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options): void {
               
        $builder
                ->add('ip')
                ->add('mac')
                ->add('port')
                ->add('instance')
        ;
/*       
 * 
 * It is possible to only show the list of available ports
 * but if the edit form is loaded, it does NOT show currently assigned port
 * instead it olny allow to select available ports
 * 
 * 
 *                      
        $address_id = '-1';
        $address = $builder->getForm()->getData();
        if($address instanceof Addresses){
            $address_id = $address->getId();
        }

        $builder
                ->add('ip')
                ->add('mac')
                ->add('port',
                        EntityType::class,
                        array('class' => Ports::class,
                            'query_builder' => function (EntityRepository $er) {
                            
                                
                                return $er->createQueryBuilder('p')
                                ->where('p.address is NULL')
                                ->orWhere('p.address = '.$address_id);
                            }))
                ->add('instance')
        ;
 * 
 */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresses::class,
        ]);
    }
}
