<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Lista zadań')
            ->setPageTitle('new', 'Dodaj nowe zadanie')
            ->setEntityLabelInSingular('zadanie')
            ->setEntityLabelInPlural('zadania')
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->addBatchAction(
                Action::new('markAsNotDone', 'Oznacz zaznaczone jako niewykonane')
                    ->linkToCrudAction('markAsNotDone')
            )
            ->addBatchAction(
                Action::new('markAsDone', 'Oznacz zaznaczone jako wykonane')
                    ->linkToCrudAction('markAsDone')
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            'restaurant' => AssociationField::new('restaurant', 'Restauracja'),
            'title' => TextField::new('title', 'Zadanie'),
            'expiresAt' => DateTimeField::new('expiresAt', 'Do kiedy należy wykonać'),
            'done' => BooleanField::new('done', 'Wykonane'),
        ];

        if ($this->isGranted(User::ROLE_ADMIN)) {
            yield $fields['restaurant'];
        }

        yield $fields['title'];
        yield $fields['expiresAt'];
        yield $fields['done'];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title'))
            ->add(DateTimeFilter::new('expiresAt'))
            ->add(BooleanFilter::new('done', 'Wykonane'))
        ;
    }

    /**
     * @param Task $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isGranted(User::ROLE_ADMIN)) {
            if ($this->isGranted(User::ROLE_OWNER)) {
                $entityInstance->setRestaurant($user->getOwnedRestaurant());
            } elseif ($this->isGranted(User::ROLE_EMPLOYEE)) {
                $entityInstance->setRestaurant($user->getWorkplaceRestaurant());
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
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

    public function markAsDone(BatchActionDto $batchActionDto): RedirectResponse
    {
        return $this->markIsDone($batchActionDto, true);
    }

    public function markAsNotDone(BatchActionDto $batchActionDto): RedirectResponse
    {
        return $this->markIsDone($batchActionDto, false);
    }

    private function markIsDone(BatchActionDto $batchActionDto, bool $done): RedirectResponse
    {
        $className = $batchActionDto->getEntityFqcn();

        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);

        foreach ($batchActionDto->getEntityIds() as $id) {
            $user = $entityManager->find($className, $id);
            $user->setDone($done);
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
