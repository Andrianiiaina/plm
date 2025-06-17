<?php

namespace App\Form;

use App\Entity\Tender;
use App\Entity\User;
use App\Event\HistoryEvent;
use App\EventSubscriber\HistorySubscriber;
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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;

class TenderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' => "Titre",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('contract_number',TextType::class, [
                'label' => "Référence",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('location',TextType::class, [
                'label' => "Adresse",
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('min_budget', NumberType::class,[
                'label' => "Budget minimum",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ])
            ->add('max_budget', NumberType::class,[
                'label' => "Budget maximum",
                'required' => false,
                'attr' => ['class'=>'form-control-sm'],
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ]
                )
            ->add('status',ChoiceType::class,[
                'attr' => ['class'=>'form-control-sm'],
                'choices'  => ListService::$tender_status,
            ])
            ->add('tender_type',ChoiceType::class,[
                'label'=>'Type du marché',
                'choices'  => ListService::$tender_type,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('url', UrlType::class,[
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'required' => false,
                'attr' => ['class'=>'form-control-sm'],
            ])
            
            ->add('description',TextareaType::class, [
                'label' => "Préstation",
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'required' => false,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tender::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_tender',
        
        ]);
    }
}
