<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Tender;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TenderDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('submissionDate', DateType::class, [
            'label' => "Date de soumission",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        ->add('responseDate', DateType::class, [
            'label' => "Date de réponse",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        ->add('negociationDate', DateType::class, [
            'label' => "Date de négociation",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        ->add('attributionDate', DateType::class, [
            'label' => "Date d'attribution",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        
        ->add('start_date', DateType::class, [
            'label' => "Date de début prévu",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        ->add('end_date', DateType::class, [
            'label' => "Date de fin prévu",
            'widget' => 'single_text',
            'required' => false,
            'constraints' => [new Assert\GreaterThanOrEqual('today') ],
        ])
        ->add('duration', NumberType::class,[
            'label' => "Durrée (en année)",
            'required' => true,
            'label_attr' => ['class'=>'col-form-label'],
            'constraints'=>[new Assert\Range([
                'min' => 0,
                'max' => 10,
                'notInRangeMessage' => 'Entrez une durrée valide',
            ])]
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tender::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_tender_date',
        ]);
    }
}
