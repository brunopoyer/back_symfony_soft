<?php

namespace App\Repository;

use App\Entity\SaleItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaleItem>
 *
 * @method SaleItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleItem[]    findAll()
 * @method SaleItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleItem::class);
    }

    public function getSaleItemsBySaleId(int $saleId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.sale_id = :saleId')
            ->setParameter('saleId', $saleId)
            ->getQuery()
            ->getResult();
    }

    public function getSaleItemsByWineId(int $wineId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.wine_id = :wineId')
            ->setParameter('wineId', $wineId)
            ->getQuery()
            ->getResult();
    }

    public function add(SaleItem $saleItem, bool $flush = false): void
    {
        $this->_em->persist($saleItem);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(SaleItem $saleItem, bool $flush = false): void
    {
        $this->_em->remove($saleItem);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
