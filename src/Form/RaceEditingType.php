<?php

namespace App\Form;

use App\Entity\Race;
use DateTimeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RaceEditingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('RaceName',TextType::class,[
                'mapped'=> false,
            ])
            ->add('RaceDate',DateType::class,[
                'mapped'=> false,
            ])
            ->add('save',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-primary float-end mt-2',
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Race::class,
        ]);
    }
}
