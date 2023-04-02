<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Bank informacji')
            ->setPageTitle('new', 'Nowy artykuł')
            ->setPageTitle('edit', 'Edycja artykułu')
            ->setEntityLabelInSingular('artykuł')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($this->isGranted(User::ROLE_ADMIN)) {
            yield AssociationField::new('restaurant', 'Restauracja');
        }

        yield ImageField::new('image', 'Zdjęcie')
            ->setUploadDir('public/uploads/articles/')
            ->setBasePath('uploads/articles/');

        yield TextField::new('title', 'Tytuł');

        yield TextField::new('slug', 'Slug');

        yield TextEditorField::new('content', 'Treść')
            ->setTemplatePath('admin/field/text_editor.html.twig');
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

    /**
     * @param Article $entityInstance
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
}
