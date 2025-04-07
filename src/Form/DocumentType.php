<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Tender;
use App\Entity\User;
use App\Repository\TenderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Validator\Constraints\File;
use App\Service\ListService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('status',ChoiceType::class,[
                'label'=>'Opération',
                'choices'  => ListService::$document_status,
            ])
            ->add('responsable', EntityType::class, [
                'label'=>"Par",
                'class' => User::class,
                'choice_label' => 'email',
            ])
           

            ->add('filepath',DropzoneType::class,[
                'label' => 'Fichier (pdf, txt, doc, docx, xls, xlsx)',
                'mapped' => false,
                'required' => !$options['is_edited'],
                
                'constraints' => [
                   
                    new File([
                        'maxSize' => '20M',
                        'extensions' => [
                            'txt' => 'text/plain',
                            'pdf',
                            "doc", "docx", "xls", "xlsx"
                        ],

                        'mimeTypesMessage' => 'Le fichier doit etre au format txt, doc, docs, xls,xslx ou pdf',
                    ])
                ],
                ])
            ->add('information',TextareaType::class, [
                'label' => "Note",
                'required' => false,
            ])
            ->add('limitDate', DateType::class, [
                'label' => "Date de fin prévu",
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            ])
        ;
        if(!$options['is_edited']){
            $builder ->add('name',TextType::class,[
                'label' => 'Titre du document',
                'disabled' => $options['is_edited'],

            ]);
        }
      
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'is_edited' =>false,
            'user'=>User::class,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_document',
        ]);
    }
}
