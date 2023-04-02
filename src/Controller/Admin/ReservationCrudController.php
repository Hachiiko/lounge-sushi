<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
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
        yield TextField::new('table.restaurant', 'Restauracja');
        yield AssociationField::new('table', 'Stolik');
        yield DateTimeField::new('beginsAt', 'Ważna od');
        yield DateTimeField::new('endsAt', 'Ważna do');

        if ($pageName === 'detail') {
            yield DateTimeField::new('confirmedAt', 'Data potwierdzenia');
        } else {
            yield BooleanField::new('confirmed', 'Potwierdzona');
        }

        yield CollectionField::new('reservationProducts', 'Produkty')->useEntryCrudForm();
        yield MoneyField::new('totalPrice', 'Cena razem')->setCurrency('PLN');
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
}
