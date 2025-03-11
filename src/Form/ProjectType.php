<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Entity\User;
use App\Service\ListService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre du projet",
                'required' => true,
            ])
            ->add('budget', IntegerType::class,[
                'label' => "Budget",
                'required' => true,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 1000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ])
            ->add('devise',ChoiceType::class,[
                'label'=>'Devise',
                'choices'  => ListService::$devise,
            ])
            ->add('deadline', DateType::class, [
                'label' => "Deadline",
                'widget' => 'single_text',
                'required' => true,
            ])
        ;
        if($options['is_edit']==true){
            $builder->add('status',EntityType::class,[
                'class' => ProjectStatus::class,
                'choice_label' => 'label',
                'multiple' => false,
            ])->add('responsable',EntityType::class,[
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => false,
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'is_edit'=> false,
        ]);
    }
}
