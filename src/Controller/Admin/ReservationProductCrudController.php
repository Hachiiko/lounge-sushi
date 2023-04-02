<?php

namespace App\Controller\Admin;

use App\Entity\ReservationProduct;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class ReservationProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ReservationProduct::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Produkty')
            ->setEntityLabelInSingular('produkt')
            ->setEntityLabelInPlural('produkty')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('product', 'Produkt'),
            NumberField::new('quantity', 'Ilość'),
        ];
    }
}
