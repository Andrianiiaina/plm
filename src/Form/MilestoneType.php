<?php

namespace App\Form;

use App\Entity\Milestone;
use App\Entity\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MilestoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => "Nom",
                'required' => true,
            ])
            ->add('due_date',DateType::class, [
                'label' => "Deadline prevu",
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('rate', IntegerType::class,[
                'label' => "Poids",
                'required' => true,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 1000,
                    'notInRangeMessage' => '[0 à 1000]',
                ])]
            ])
        ;
        if($options['is_edit'] === true){
            $builder->add('status', EntityType::class, [
                'class' => TaskStatus::class,
                'choice_label' => 'label',
                'multiple' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Milestone::class,
            'is_edit'=>false,
        ]);
    }
}
