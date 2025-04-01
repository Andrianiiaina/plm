<?php

namespace App\Controller\Admin;

use App\Entity\Tender;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TenderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tender::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('contract_number'),
            TextEditorField::new('description'),
            TextField::new('location'),
            TextField::new('url'),
            TextField::new('status'),
            TextField::new('tender_type'),
            NumberField::new('duration'),
            IntegerField::new('min_budget'),
            IntegerField::new('max_budget'),
            DateTimeField::new('start_date'),
            DateTimeField::new('submissionDate'),
            DateTimeField::new('responseDate'),
            DateTimeField::new('attributionDate'),
            DateTimeField::new('negociationDate'),
            DateTimeField::new('end_date'),
            AssociationField::new('responsable'),
            AssociationField::new('documents'),
            
        ];
    }
}
