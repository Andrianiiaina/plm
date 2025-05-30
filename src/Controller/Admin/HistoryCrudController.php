<?php

namespace App\Controller\Admin;

use App\Entity\History;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action as ACTION;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return History::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(ACTION::NEW, ACTION::EDIT) ;
           
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('actor_id'),
            IntegerField::new('type'),
            IntegerField::new('type_id'),
            TextField::new('actions'),
            DateTimeField::new('date'),
        ];
    }
    
}
