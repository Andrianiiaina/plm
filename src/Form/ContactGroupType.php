<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\ContactGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name',TextType::class, [
                'label' => "Nom du groupe",
            ])
            ->add('contacts', EntityType::class, [
                'class' => Contact::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactGroup::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_calendar_group',
        ]);
    }
}
