<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Form\Type\ReservationType;
use App\Repository\ReservationRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/{slug}', name: 'restaurant_show')]
    public function show(Request $request, Restaurant $restaurant, ReservationRepository $reservationRepository): Response
    {
        $reservationForm = $this->createForm(ReservationType::class, new Reservation);
        $reservationForm->handleRequest($request);

        if ($reservationForm->isSubmitted() && $reservationForm->isValid()) {
            $reservation = $reservationForm->getData();
            $reservation->setRestaurant($restaurant);

            $reservationRepository->save($reservation, true);

            $this->addFlash('success', 'Rezerwacja została złożona. Oczekuj na telefon w celu jej potwierdzenia.');

            return $this->redirectToRoute('restaurant_show', ['slug' => $restaurant->getSlug()]);
        }

        return $this->render('frontend/show.html.twig', [
            'restaurant' => $restaurant,
            'reservationForm' => $reservationForm,
        ]);
    }

    #[Route('/{slug}/{articleSlug}', name: 'restaurant_article')]
    public function article(Restaurant $restaurant, #[MapEntity(mapping: ['articleSlug' => 'slug'])] Article $article): Response
    {
        return $this->render('frontend/article.html.twig', [
            'restaurant' => $restaurant,
            'article' => $article,
        ]);
    }
}