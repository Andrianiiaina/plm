<?php

namespace App\Controller\Admin;

use App\Entity\ContactGroup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class ContactGroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactGroup::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('Name'), 
            AssociationField::new('contacts')->setFormTypeOptions([
                'by_reference' => false, 
            ])
        ];
    }
}
