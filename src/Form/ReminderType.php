<?php

namespace App\Form;

use App\Entity\Reminder;
use App\Service\ListService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReminderType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder
            ->add('day_before', ChoiceType::class, [
                  'attr' => ['class'=>'col-md-3 col-form-label'],
                  'choices' => ListService::$reminders,
            ])
            ->add('date_type',ChoiceType::class,[
                'attr' => ['class'=>'col-md-3 col-form-label'],
                'choices'=>ListService::$tender_date_type,
                'disabled'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reminder::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_reminder_tender',
        ]);
    }
}
