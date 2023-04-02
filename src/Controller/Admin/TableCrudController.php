<?php

namespace App\Controller\Admin;

use App\Entity\Table;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
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
            ->setPageTitle('new', 'Nowy stolik')
            ->setPageTitle('edit', 'Edycja stolika')
            ->setEntityLabelInSingular('stolik')
            ->setEntityLabelInPlural('stoliki')
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::NEW, User::ROLE_ADMIN)
            ->setPermission(Action::EDIT, User::ROLE_ADMIN)
            ->setPermission(Action::DELETE, User::ROLE_ADMIN)
            ->setPermission(Action::BATCH_DELETE, User::ROLE_ADMIN)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($this->isGranted(User::ROLE_ADMIN)) {
            yield AssociationField::new('restaurant', 'Restauracja');
        }

        yield TextField::new('name', 'Nazwa');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted(User::ROLE_ADMIN)) {
            return $queryBuilder;
        }

        $queryBuilder->innerJoin('entity.restaurant', 'restaurant');

        if ($this->isGranted(User::ROLE_OWNER)) {
            $queryBuilder->andWhere('restaurant.owner = :user');
        } elseif ($this->isGranted(User::ROLE_EMPLOYEE)) {
            $queryBuilder->andWhere(':user member of restaurant.employees');
        }

        $queryBuilder->setParameter('user', $this->getUser());

        return $queryBuilder;
    }
}
