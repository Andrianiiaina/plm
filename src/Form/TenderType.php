<?php

namespace App\Form;

use App\Entity\Tender;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\ListService;
use Symfony\Component\Validator\Constraints as Assert;

class TenderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' => "Titre du projet",
                'required' => true,
            ])
            ->add('contract_number',TextType::class, [
                'label' => "Référence",
                'required' => true,
            ])
            ->add('description',TextareaType::class, [
                'label' => "Préstation",
                'required' => false,
            ])
            ->add('location',TextType::class, [
                'label' => "Adresse",
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'required' => true,
            ])
            ->add('min_budget', IntegerType::class,[
                'label' => "Budget minimum",
                'required' => true,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ])
            ->add('max_budget', IntegerType::class,[
                'label' => "Budget maximum",
                'required' => false,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ]
                )
            ->add('status',ChoiceType::class,[
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'choices'  => ListService::$tender_status,
            ])
            ->add('tender_type',ChoiceType::class,[
                'label'=>'Type de marché',
                'choices'  => ListService::$tender_type,
            ])
            ->add('url', UrlType::class,[
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],

            ])
            ->add('responsable', EntityType::class, [
                'label'=>'Bid Manager responsable',
                'class' => User::class,
                'choice_label' => 'email',
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
            ])
           
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tender::class,
        ]);
    }
}
