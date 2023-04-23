<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\UserRepository;
use chillerlan\QRCode\QRCode;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
class RestaurantCrudController extends AbstractCrudController
{
    public function __construct(
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {
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
        yield TextEditorField::new('description', 'Opis')
            ->setTemplatePath('admin/field/text_editor.html.twig');
        yield TextField::new('address', 'Adres');
        yield TextField::new('city', 'Miasto');
        yield TextField::new('postcode', 'Kod pocztowy');
        yield TextField::new('phone', 'Numer telefonu');
        yield AssociationField::new('owner', 'Właściciel');
        yield AssociationField::new('employees', 'Pracownicy')
            ->setQueryBuilder(fn (QueryBuilder $queryBuilder) => $this->userRepository->addRoleCriteria($queryBuilder, User::ROLE_EMPLOYEE));

        if ($pageName !== 'index' && $pageName !== 'detail') {
            yield TextField::new('googleMapsEmbedCode', 'Kod umieszczania Google Maps');
        }

        if ($pageName === 'detail') {
            $restaurant = $this->getContext()->getEntity()->getInstance();

            $restaurantUrl = $this->urlGenerator->generate('restaurant_show', [
                'slug' => $restaurant->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            yield UrlField::new('menuQrCode', 'Link do strony')
                ->setValue($restaurantUrl);

            yield UrlField::new('menuQrCode', 'Kod QR do menu')
                ->setValue((new QRCode())->render($restaurantUrl . '#book'))
                ->setCustomOption('url', $restaurantUrl . '#book')
                ->setTemplatePath('admin/field/menu_qr_code.html.twig');
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('redirectToRestaurantPage', 'Przejdź do strony')
                    ->linkToCrudAction('redirectToRestaurantPage')
            )
        ;
    }

    public function redirectToRestaurantPage(AdminContext $context): RedirectResponse
    {
        $restaurant = $this->getContext()->getEntity()->getInstance();

        return $this->redirectToRoute('restaurant_show', ['slug' => $restaurant->getSlug()]);
    }
}
