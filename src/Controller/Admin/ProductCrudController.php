<?php

namespace App\Controller\Admin;

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
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Katalog produktów')
            ->setPageTitle('new', 'Nowy produkt')
            ->setPageTitle('edit', 'Edycja produktu')
            ->setEntityLabelInSingular('produkt')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            'restaurant' => AssociationField::new('restaurant', 'Restauracja'),
            'name' => TextField::new('name', 'Nazwa'),
            'description' => TextEditorField::new('description', 'Opis')
                ->setTemplatePath('admin/field/text_editor.html.twig'),
            'price' => MoneyField::new('price', 'Cena')
                ->setCurrency('PLN'),
            'image' => ImageField::new('image', 'Zdjęcie')
                ->setUploadDir('public/uploads/products/')
                ->setBasePath('uploads/products/'),
        ];

        if ($this->isGranted(User::ROLE_ADMIN)) {
            yield $fields['restaurant'];
        }

        if ($pageName === 'index') {
            yield $fields['image'];
            yield $fields['name'];
            yield $fields['description'];
            yield $fields['price'];
        } else {
            yield $fields['name'];
            yield $fields['description'];
            yield $fields['price'];
            yield $fields['image'];
        }
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
     * @param Product $entityInstance
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
