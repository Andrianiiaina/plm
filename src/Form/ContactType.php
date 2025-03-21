<?php

namespace App\Form;

use App\Entity\Contact;

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
            ])
            ->add('email',EmailType::class, [
                'label' => "Addresse Email",
                'required' => true,
            ]) 
            ->add('contact_perso',TextType::class, [
                'label' => "Contact personnel",
            ])
            ->add('contact_pro',TextType::class, [
                'label' => "Contact professionnel",
                'required' => false,
            ])
            ->add('organisation',TextType::class, [
                'required' => true,
                'label' => "Nom de la compagnie",
            ])
            ->add('function',TextType::class, [
                'label' => "Poste",
                'required' => true,
            ])
           // ->add('parent', EntityType::class, [
            //    'label' => "Supérieur",
           //     'class' => Contact::class,
           //     'choice_label' => 'name',
           // ])
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
