<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RestaurantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Restaurant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Restauracje')
            ->setEntityLabelInSingular('restaurację')
            ->setEntityLabelInPlural('restauracje')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('logo', 'Logo')
            ->setUploadDir('public/uploads/restaurants/')
            ->setBasePath('uploads/restaurants/');
        yield TextField::new('name', 'Nazwa');
        yield TextField::new('address', 'Adres');
        yield TextField::new('city', 'Miasto');
        yield TextField::new('postcode', 'Kod pocztowy');
        yield TextField::new('phone', 'Numer telefonu');
    }
}
