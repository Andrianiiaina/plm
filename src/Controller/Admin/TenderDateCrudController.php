<?php

namespace App\Controller\Admin;

use App\Entity\TenderDate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class TenderDateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TenderDate::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW) ;
           
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('tender')->setFormTypeOptions([
                'by_reference' => false, 
            ])->hideOnForm(),
            DateTimeField::new('start_date'),
            DateTimeField::new('submissionDate'),
            DateTimeField::new('responseDate'),
            DateTimeField::new('attributionDate'),
            DateTimeField::new('negociationDate'),
            DateTimeField::new('end_date'),
            NumberField::new('duration'),
        ];
    }
    
}
