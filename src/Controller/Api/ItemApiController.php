<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ItemApiController extends AbstractController
{
    // #[Route('/api/item/api', name: 'app_api_item_api')]
    // public function index(): Response
    // {
    //     return $this->render('api/item_api/index.html.twig', [
    //         'controller_name' => 'Api/ItemApiController',
    //     ]);
    // }

    #[Route('/api/items', name: 'app_api_items', methods: ['GET'])]
    public function items(ItemRepository $repo): Response
    {
        return $this->json($repo->findAll());
    }
}
