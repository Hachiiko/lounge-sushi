<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
            ->setEntityLabelInSingular('artykuł')
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('restaurant', 'Restauracja');

        yield ImageField::new('image', 'Zdjęcie')
            ->setUploadDir('public/uploads/articles/')
            ->setBasePath('uploads/articles/');

        yield TextField::new('title', 'Tytuł');

        yield TextEditorField::new('content', 'Treść')
            ->setTemplatePath('admin/field/text_editor.html.twig');
    }
}
