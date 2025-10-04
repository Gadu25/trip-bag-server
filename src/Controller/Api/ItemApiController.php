<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ItemApiController extends AbstractController
{
    // #[Route('/api/item/api', name: 'app_api_item_api')]
    // public function index(): Response
    // {
    //     return $this->render('api/item_api/index.html.twig', [
    //         'controller_name' => 'Api/ItemApiController',
    //     ]);
    // }

    #[Route('/api/item/{id}', name: 'app_api_item', methods: ['GET'])]
    public function item(EntityManagerInterface $entityManager, int $id): Response
    {
        $item = $entityManager->getRepository(Item::class)->find($id);

        if (!$item) {
            return $this->json(['error' => "Can't find item for id ".$id], 400);
        }

        return $this->json([
            'data' => $item
        ], 201);
    }

    #[Route('/api/items', name: 'app_api_items', methods: ['GET'])]
    public function items(ItemRepository $repo): Response
    {
        return $this->json($repo->findAll());
    }

    #[Route('/api/item', name: 'app_api_add_item', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $this->validate($data);

        $item = new Item();
        $item->setName($data['name']);
        $item->setCategory($data['category']);
        $item->setIsPacked(false);

        $entityManager->persist($item);
        $entityManager->flush();

        return $this->json([
            'message' => 'New item added.',
            'data' => [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'category' => $item->getCategory(),
                'isPacked' => $item->isPacked(),
            ]
        ], 201);
    }

    #[Route('/api/item/{id}', name: 'app_api_update_item', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $item = $entityManager->getRepository(Item::class)->find($id);

        if (!$item) {
            return $this->json(['error' => "Can't find item for id ".$id], 400);
        }
        $data = json_decode($request->getContent(), true);
        $this->validate($data);

        $item->setName($data['name']);
        $item->setCategory($data['category']);
        $item->setIsPacked($data['isPacked']);

        $entityManager->flush();

        return $this->json([
            'message' => 'Item is updated.',
            'data' => [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'category' => $item->getCategory(),
                'isPacked' => $item->isPacked(),
            ]
        ], 201);
    }

    #[Route('/api/item/toggle/{id}', name: 'app_api_toggle_item', methods: ['PATCH'])]
    public function toggle(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $item = $entityManager->getRepository(Item::class)->find($id);

        if (!$item) {
            return $this->json(['error' => "Can't find item for id ".$id], 400);
        }

        $data = json_decode($request->getContent(), true);

        if(!$data || !isset($data['isPacked']))
        {
            return $this->json(['error' => 'Invalid input'], 400);
        }

        $item->setIsPacked($data['isPacked']);

        $entityManager->flush();

        return $this->json([
            'message' => 'Item is updated.',
            'data' => [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'category' => $item->getCategory(),
                'isPacked' => $item->isPacked(),
            ]
        ], 201);
    }

    #[Route('/api/item/{id}', name: 'app_api_delete_item', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $item = $entityManager->getRepository(Item::class)->find($id);
        if (!$item) {
            return $this->json(['error' => "Can't find the item for id ".$id], 400);
        }
        $entityManager->remove($item);
        $entityManager->flush();

        return $this->json([
            'message' => 'Item is succesfully deleted.'
        ]);
    }

    private function validate($data) {
        if (!$data || !isset($data['name']) || !isset($data['category'])) {
            return $this->json(['error' => 'Invalid input'], 400);
        }
    }
}
