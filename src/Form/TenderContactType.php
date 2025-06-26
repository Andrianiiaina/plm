<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TenderContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'contact',EntityType::class,[
                    'class' => Contact::class,
                    'choice_label' => 'email',
                    'multiple' => false,
                    'attr'=>['class'=>'form-control-sm  mb-3'],
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_contact_tender',
        ]);
    }
}
