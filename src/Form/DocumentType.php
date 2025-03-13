<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Tender;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Validator\Constraints\File;
use App\Service\ListService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tender', EntityType::class, [
                'label'=>"Le projet associé à ce document",
                'class' => Tender::class,
                'choice_label' => 'title',
                'disabled' => $options['is_edited'],
            ])
            ->add('status',ChoiceType::class,[
                'label'=>'Opération',
                'label_attr' => ['class'=>'col-sm-3 col-form-label'],
                'choices'  => ListService::$document_status,
            ])
            ->add('responsable', EntityType::class, [
                'label'=>"Par:",
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('name',TextType::class,[
                'label' => 'Titre du document',
                'disabled' => $options['is_edited'],
                ])

            ->add('filepath',DropzoneType::class,[
                'label' => 'Fichier (pdf ou txt)',
                'mapped' => false,
                'required' => false,
                
                'constraints' => [
                   
                    new File([
                        'maxSize' => '5M',
                        'extensions' => [
                            'txt' => 'text/plain',
                            'pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit etre au format text ou pdf',
                    ])
                ],
                ])
            ->add('information',TextareaType::class, [
                'label' => "Note",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'is_edited' =>false,
        ]);
    }
}
