<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Rezerwacje')
            ->setPageTitle('new', 'Nowa rezerwacja')
            ->setPageTitle('edit', 'Edycja rezerwacji')
            ->setEntityLabelInSingular('rezerwację')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === 'index' || $pageName === 'detail') {
            yield TextField::new('restaurant', 'Restauracja');
        }

        yield AssociationField::new('table', 'Stolik');
        yield TextField::new('name', 'Imię i nazwisko');
        yield TextField::new('phone', 'Telefon');
        yield NumberField::new('numberOfPeople', 'Ilość osób');
        yield DateTimeField::new('beginsAt', 'Ważna od');
        yield DateTimeField::new('endsAt', 'Ważna do');

        if ($pageName === 'detail') {
            yield DateTimeField::new('confirmedAt', 'Data potwierdzenia');
        } else {
            yield BooleanField::new('confirmed', 'Potwierdzona');
        }

        yield CollectionField::new('reservationProducts', 'Produkty')->useEntryCrudForm();

        if ($pageName === 'index' || $pageName === 'detail') {
            yield MoneyField::new('totalPrice', 'Cena razem')->setCurrency('PLN');
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('table'))
            ->add(DateTimeFilter::new('beginsAt'))
            ->add(DateTimeFilter::new('endsAt'))
            ->add(DateTimeFilter::new('confirmedAt'))
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted(User::ROLE_ADMIN)) {
            return $queryBuilder;
        }

        $queryBuilder
            ->innerJoin('entity.table', 'table')
            ->innerJoin('table.restaurant', 'restaurant');

        if ($this->isGranted(User::ROLE_OWNER)) {
            $queryBuilder->andWhere('restaurant.owner = :user');
        } elseif ($this->isGranted(User::ROLE_EMPLOYEE)) {
            $queryBuilder->andWhere(':user member of restaurant.employees');
        }

        $queryBuilder->setParameter('user', $this->getUser());

        return $queryBuilder;
    }
}
