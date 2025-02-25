<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\ListService;

class ProjectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'label' => "Titre du projet",
                'required' => true,
            ])
            ->add('contract_number',TextType::class, [
                'label' => "Numéro du marché",
                'required' => true,
            ])
            ->add('description',TextareaType::class, [
                'label' => "Description",
                'required' => false,
            ])
            ->add('location',TextType::class, [
                'label' => "Localisation",
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'required' => true,
            ])
            ->add('start_date', DateType::class, [
                'label' => "Date de début",
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('end_date', DateType::class, [
                'label' => "Date de fin",
                'widget' => 'single_text',
            ])
            ->add('min_budget', IntegerType::class,[
                'label' => "Budget minimum",
                'required' => true,
            ])
            ->add('max_budget', IntegerType::class,[
                'label' => "Budget maximum"])
            ->add('status',ChoiceType::class,[
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'choices'  => ListService::$project_status,
            ])
            ->add('project_type',ChoiceType::class,[
                'label'=>'Type du projet',
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'choices'  => ListService::$project_type,
            ])
            ->add('url', UrlType::class,[
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],

            ])
            ->add('responsable_id', EntityType::class, [
                'label'=>'Responsable',
                'class' => User::class,
                'choice_label' => 'email',
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
