<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    #[Route('/{slug}', name: 'restaurant_show')]
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('frontend/show.html.twig', [
            'restaurant' => $restaurant,
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