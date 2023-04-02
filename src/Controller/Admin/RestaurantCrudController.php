<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RestaurantCrudController extends AbstractCrudController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Restaurant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Restauracje')
            ->setPageTitle('new', 'Nowa restauracja')
            ->setPageTitle('edit', 'Edycja restauracji')
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
        yield AssociationField::new('owners', 'Właściciele')
            ->setQueryBuilder(fn (QueryBuilder $queryBuilder) => $this->userRepository->addRoleCriteria($queryBuilder, User::ROLE_OWNER));
        yield AssociationField::new('employees', 'Pracownicy')
            ->setQueryBuilder(fn (QueryBuilder $queryBuilder) => $this->userRepository->addRoleCriteria($queryBuilder, User::ROLE_EMPLOYEE));
    }
}
