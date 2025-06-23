<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class DocumentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, ACTION::EDIT) ;
           
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('filename'),
            TextField::new('status'),
            TextEditorField::new('information'),
            AssociationField::new('responsable'),
            AssociationField::new('tender'),
        ];
    }
}
