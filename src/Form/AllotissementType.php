<?php

namespace App\Form;
use App\Entity\Allotissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AllotissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number',TextType::class,[
                'label' => "Numéro de l'allotissement",
                'required' => true,
            ])
            ->add('title',TextType::class,[
                'label' => "Titre",
                'required' => true,
            ])
            ->add('description',TextareaType::class, [
                'label' => "Préstation",
                'required' => false,
            ])
            ->add('minBudget',IntegerType::class,[
                'label' => "Budget minimum",
                'required' => true,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ])
            ->add('maxBudget', IntegerType::class,[
                'label' => "Budget maximum",
                'required' => false,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 100000000000000,
                    'notInRangeMessage' => 'Entrez un budget valide',
                ])]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Allotissement::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_allotissement',
        ]);
    }
}
