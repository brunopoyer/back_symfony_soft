<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Entity\SaleItem;
use App\Repository\SaleItemRepository;
use App\Repository\SaleRepository;
use App\Repository\WineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SaleController extends AbstractController
{

    protected SaleRepository $saleRepository;

    protected SaleItemRepository $saleItemRepository;
    protected WineRepository $wineRepository;

    public function __construct(SaleRepository $saleRepository, WineRepository $wineRepository, SaleItemRepository $saleItemRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->wineRepository = $wineRepository;
        $this->saleItemRepository = $saleItemRepository;
    }
    public function getRequestType(Request $request): array
    {
        $contentType = $request->headers->get('Content-Type');
        if ($contentType === 'application/json') {
            return $request->toArray();
        }

        return $request->request->all();
    }

    #[Route('/sales', name: 'app_sale', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $data = $this->saleRepository->findAll();
        $dataNew = [];
        foreach ($data as $sale) {
            $items = $this->saleItemRepository->getSaleItemsBySaleId($sale->getId());
            if ($items) {
                foreach ($items as $item) {
                    $wine = $this->wineRepository->find($item->getWineId());
                    if ($wine) {
                        $item->wine = $wine;
                    }
                }
                $sale->items = $items;
                $dataNew[] = $sale;
            }
        }

        return $this->json([
            'data' => $dataNew,
        ]);
    }

    #[Route('/sales', name: 'app_sale_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->getRequestType($request);

        // Verify if all fields are present
        if (!isset($data['items']) || !isset($data['distance'])) {
            return $this->json([
                'message' => 'Faltam campos requeridos!',
            ], 400);
        }

        $total = 0;
        $wineWeight = 0;
        foreach ($data['items'] as $item) {
            if (!isset($item['wine']['id']) || !isset($item['quantity'])) {
                return $this->json([
                    'message' => 'Faltam campos requeridos!',
                ], 400);
            }
            $wine = $this->wineRepository->find($item['wine']['id']);
            if (!$wine) {
                return $this->json([
                    'message' => 'Vinho não encontrado!',
                ], 404);
            }

            $wineWeight += $wine->getWeight();

            $total += ($item['quantity'] * $wine->getPrice());

            $saleItem = new SaleItem();
            $saleItem->setWineId($item['wine']['id']);
            $saleItem->setQuantity($item['quantity']);
            $saleItem->setUnitPrice($wine->getPrice());

            $salesItens[] = $saleItem;
        }

        if ($data['distance'] > 100) {
            $freight = (floor($wineWeight) * 5) + (($data['distance'] * $wineWeight) / 100);
        } else {
            $freight = (floor($wineWeight) * 2);
        }

        $freight = round($freight, 2);

        $total += $freight;

        // Create sale
        $sale = new Sale();
        $sale->setTotal($total);
        $sale->setDistance($data['distance']);
        $sale->setFreight($freight);
        $sale->updateTimeStamps();

        $this->saleRepository->add($sale, true);

        // Create sale items
        foreach ($salesItens as $item) {
            $item->setSaleId($sale->getId());

            $this->saleItemRepository->add($item, true);
        }

        return $this->json([
            'message' => 'Venda Realizada com sucesso!, o total da sua venda foi de: R$' . $total,
            'data' => $sale,
        ], 201);
    }

    #[Route('/sales/{sale}', name: 'app_sale_delete', methods: ['DELETE'])]
    public function delete(int $sale): JsonResponse
    {
        $sale = $this->saleRepository->find($sale);

        if (!$sale) {
            return $this->json([
                'message' => 'Venda não encontrada!',
            ], 404);
        }

        $items = $this->saleItemRepository->getSaleItemsBySaleId($sale->getId());
        if ($items) {
            foreach ($items as $item) {
                $this->saleItemRepository->remove($item, true);
            }
        }

        $this->saleRepository->remove($sale, true);

        return $this->json([
            'message' => 'Venda excluída com sucesso!',
        ]);
    }
}
