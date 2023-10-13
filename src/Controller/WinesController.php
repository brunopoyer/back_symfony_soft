<?php

namespace App\Controller;

use App\Entity\Wine;
use App\Repository\SaleItemRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\WineRepository;

class WinesController extends AbstractController
{
    protected WineRepository $wineRepository;
    protected SaleItemRepository $salesItemRepository;
    public function __construct(WineRepository $wineRepository, SaleItemRepository $salesItemRepository)
    {
        $this->wineRepository = $wineRepository;
        $this->salesItemRepository = $salesItemRepository;
    }

    public function getRequestType(Request $request): array
    {
        $contentType = $request->headers->get('Content-Type');
        if ($contentType === 'application/json') {
            return $request->toArray();
        }

        return $request->request->all();
    }
    #[Route('/wines', name: 'wines_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'data' => $this->wineRepository->findAll(),
        ]);
    }

    #[Route('/wines', name: 'wines_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->getRequestType($request);

        // Verify if all fields are present
        if (!isset($data['name']) || !isset($data['type']) || !isset($data['weight']) || !isset($data['price'])) {
            return $this->json([
                'message' => 'Falta campos obrigatórios!',
            ], 400);
        }

        // Verify if wine already exists
        $wine = $this->wineRepository->findBy(['name' => $data['name']]);
        if ($wine) {
            return $this->json([
                'message' => 'Vinho já existe!',
            ], 400);
        }

        // Create wine
        $wine = new Wine();
        $wine->setName($data['name']);
        $wine->setType($data['type']);
        $wine->setWeight($data['weight']);
        $wine->setPrice($data['price']);
        $wine->updateTimeStamps();

        $this->wineRepository->add($wine, true);

        return $this->json([
            'message' => 'Vinho criado com sucesso!',
            'data' => $wine,
        ], 201);
    }

    #[Route('/wines/{wine}', name: 'wines_delete', methods: ['DELETE'])]
    public function delete(int $wine): JsonResponse
    {
        $wine = $this->wineRepository->find($wine);

        if (!$wine) {
            return $this->json([
                'message' => 'Vinho não encontrado!',
            ], 404);
        }

        // Verifica se não está em algum pedido
        $salesItem = $this->salesItemRepository->getSaleItemsByWineId($wine->getId());

        if ($salesItem) {
            return $this->json([
                'message' => 'Vinho não pode ser deletado, pois está em um pedido!',
            ], 400);
        }

        $this->wineRepository->remove($wine, true);

        return $this->json([
            'message' => 'O vinho foi deletado com sucesso!',
            'data' => $wine->getName(),
        ], 201);
    }
}
