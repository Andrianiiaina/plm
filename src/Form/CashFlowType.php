<?php

namespace App\Form;

use App\Entity\CashFlow;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CashFlowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label',TextType::class, [
                'label' => "Libellé",
                'required' => true,
            ])
            ->add('amount', IntegerType::class,[
                'label' => "Montant",
                'required' => true,
                'constraints'=>[new Assert\Range([
                    'min' => 0,
                    'max' => 1000000000,
                    'notInRangeMessage' => 'Entrez un montant valide',
                ])]
            ])
            ->add('description',TextareaType::class, [
                'label' => "Description",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CashFlow::class,
        ]);
    }
}
