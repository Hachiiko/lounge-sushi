<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\Table;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\ReservationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private ReservationRepository $reservationRepository,
    ) {
    }

    public function configureAssets(): Assets
    {
        $assets = parent::configureAssets();
        $assets->addWebpackEncoreEntry('app');

        return $assets;
    }

    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $currentYear = (int) date('Y');

        $months = [
            'Styczeń',
            'Luty',
            'Marzec',
            'Kwiecień',
            'Maj',
            'Czerwiec',
            'Lipiec',
            'Sierpień',
            'Wrzesień',
            'Październik',
            'Listopad',
            'Grudzień',
        ];

        $chart->setData([
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Ilość potwierdzonych rezerwacji',
                    'backgroundColor' => '#025E30',
                    'borderColor' => '#025E30',
                    'data' => array_combine(
                        $months,
                        $this->reservationRepository->getConfirmedReservationCountPerMonth($currentYear),
                    ),
                ], [
                    'label' => 'Ilość niepotwierdzonych rezerwacji',
                    'backgroundColor' => '#820101',
                    'borderColor' => '#820101',
                    'data' => array_combine(
                        $months,
                        $this->reservationRepository->getNotConfirmedReservationCountPerMonth($currentYear),
                    ),
                ],
            ],
        ]);

        return $this->render('admin/dashboard.html.twig', [
            'chart' => $chart,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span class="h2 pe-2">@</span> Lounge Sushi')
            ->renderContentMaximized()
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Kokpit', 'fa-solid fa-chart-line'),
            MenuItem::linkToCrud('Restauracje', 'fa-solid fa-shop', Restaurant::class)->setPermission(User::ROLE_ADMIN),
            MenuItem::linkToCrud('Stoliki', 'fa-solid fa-chair', Table::class),
            MenuItem::linkToCrud('Harmonogram rezerwacji', 'fa-solid fa-calendar-days', Reservation::class),
            MenuItem::linkToCrud('Bank informacji', 'fa-regular fa-rectangle-list', Article::class),
            MenuItem::linkToCrud('Katalog produktów', 'fa-solid fa-basket-shopping', Product::class),
            MenuItem::linkToCrud('Lista zadań', 'fa-solid fa-users', Task::class),
        ];
    }
}
