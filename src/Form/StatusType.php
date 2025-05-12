<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Tender;
use App\Entity\User;
use App\Service\ListService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status',ChoiceType::class,[
            'attr' => ['class' => 'form-select status-select'],
            'choices'  => ListService::$tender_status,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tender::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_tender_status',

        ]);
    }
}
