<?php

namespace App\Controller\Admin;

use App\Entity\Table;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TableCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Table::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Stoliki')
            ->setPageTitle('new', 'Nowy stoli')
            ->setPageTitle('edit', 'Edycja stolika')
            ->setEntityLabelInSingular('stolik')
            ->setEntityLabelInPlural('stoliki')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('restaurant', 'Restauracja'),
            TextField::new('name', 'Nazwa'),
        ];
    }
}
