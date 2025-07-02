<?php

namespace App\Form;

use App\Entity\TenderDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class TenderDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('submissionDate', DateTimeType::class, [
            'label' => "Date de soumission",
            'widget' => 'single_text',
            'required' => false,
            'attr' => ['class'=>'form-control-sm  mb-3'],
        ])

        ->add('responseDate', DateTimeType::class, [
            'label' => "Date de réponse",
            'widget' => 'single_text',
            'required' => false,
            #'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            'attr' => ['class'=>'form-control-sm  mb-3'],
        ])
        ->add('negociationDate', DateTimeType::class, [
            'label' => "Date de négociation",
            'widget' => 'single_text',
            'required' => false,
            #'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            'attr' => ['class'=>'form-control-sm  mb-3'],
        ])
        ->add('attributionDate', DateTimeType::class, [
            'label' => "Date d'attribution",
            'widget' => 'single_text',
            'required' => false,
            #'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            'attr' => ['class'=>'form-control-sm mb-3'],
        ])
        
        ->add('start_date', DateTimeType::class, [
            'label' => "Date de début prévu",
            'widget' => 'single_text',
            'required' => false,
            #'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            'attr' => ['class'=>'form-control-sm  mb-3'],
        ])
        ->add('end_date', DateTimeType::class, [
            'label' => "Date de fin prévu",
            'widget' => 'single_text',
            'required' => false,
            #'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            'attr' => ['class'=>'form-control-sm  mb-3'],
        ])
        ->add('duration', NumberType::class,[
            'label' => "Durrée du projet (en année)",
            'required' => true,
            'label_attr' => ['class'=>'col-form-label'],
            'attr' => ['class'=>'form-control-sm  mb-3'],
            'constraints'=>[new Assert\Range([
                'min' => 0,
                'max' => 10,
                'notInRangeMessage' => 'Entrez une durrée valide',
                ])
            ]
        ])
       // ->add('reminder', CollectionType::class, [
       //        'entry_type' => ReminderType::class,
       //         'entry_options' => ['label' => false],
        //        'allow_add' => true,
        //        'by_reference'=>false,
        //        'allow_delete' => true,
        //    ])
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TenderDate::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_tender_date',
        ]);
    }
}
