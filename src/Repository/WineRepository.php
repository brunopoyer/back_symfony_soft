<?php

namespace App\Repository;

use App\Entity\Wine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wine>
 *
 * @method Wine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wine[]    findAll()
 * @method Wine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wine::class);
    }

    public function add(Wine $wine, bool $flush = false): void
    {
        $this->_em->persist($wine);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Wine $wine, bool $flush = false): void
    {
        $this->_em->remove($wine);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
