<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\Tender;
use App\Repository\TenderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\QueryBuilder;
class CalendarType extends AbstractType
{
    public $tenderRepository;
    public function __construct(TenderRepository $tenderRepository)
    {
        $this->tenderRepository=$tenderRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('beginAt', DateTimeType::class, [
                'input'  => 'datetime',
                'label'=> 'Le',
                'widget' => 'single_text',
                'constraints' => [new Assert\GreaterThanOrEqual('today') ],
            ])
            ->add('endAt', DateTimeType::class, [
                'label'=> "jusqu'à",
                'widget' => 'single_text',
                'required'=>false,
            ])
            ->add('title',TextType::class,['label'=> "Titre"])
            ->add('tender', EntityType::class, [
                'class' => Tender::class,
                'choices' => $this->tenderRepository->findTendersByRespo($options['user']),
                'choice_label' => 'title',
                'multiple' => false,
                'disabled' => $options['is_edited'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'is_edited' => false,
            'user' => null,
            'csrf_protection' => true, 
            'csrf_token_id' => 'form_calendar',
        ]);
    }
}
