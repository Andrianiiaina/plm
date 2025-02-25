<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Dropzone\Form\DropzoneType;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filepath',DropzoneType::class,[
                'label' => 'Fichier (pdf ou txt)',
                'mapped' => false,
                'required' => true,
                
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez insérer le fichier.',
                    ]),
                    new ConstraintsFile([
                        'maxSize' => '5M',
                        'extensions' => [
                            'txt' => 'text/plain',
                            'pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit etre au format text ou pdf',
                    ])
                ],
                ])
            ->add('url',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
