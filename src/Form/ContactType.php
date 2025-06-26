<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Tender;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => "Nom",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('email',EmailType::class, [
                'label' => "Addresse Email",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ]) 
            ->add('contact_perso',TextType::class, [
                'label' => "Contact personnel",
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('contact_pro',TextType::class, [
                'label' => "Contact professionnel",
                'required' => false,
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('organisation',TextType::class, [
                'required' => true,
                'label' => "Nom de la compagnie",
                'attr' => ['class'=>'form-control-sm'],
            ])
            ->add('function',TextType::class, [
                'label' => "Poste",
                'required' => true,
                'attr' => ['class'=>'form-control-sm'],
            ])
            
          /**
           *  ->add('tender', EntityType::class, [
                *'label' => "Tenders",
               * 'class' => Tender::class,
              *  'choice_label' => 'title',
             *   'multiple'=>true,
            *    'disabled'=>true
           *])
           */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_contact',
        ]);
    }
}
