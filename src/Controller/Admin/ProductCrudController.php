<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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

        if ($pageName === 'index') {
            return [
                $fields['restaurant'],
                $fields['image'],
                $fields['name'],
                $fields['description'],
                $fields['price'],
            ];
        }

        return $fields;
    }
}
