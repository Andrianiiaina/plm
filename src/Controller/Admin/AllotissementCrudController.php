<?php

namespace App\Controller\Admin;

use App\Entity\Allotissement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AllotissementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Allotissement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('number'),
            TextField::new('title'),
            TextEditorField::new('description'),
            IntegerField::new('minBudget'),
            IntegerField::new('maxBudget'),
            AssociationField::new('tender'),
        ];
    }
}
