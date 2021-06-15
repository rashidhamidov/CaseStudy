<?php

namespace App\Repository;

use App\Entity\ProviderAPI;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProviderAPI|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderAPI|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderAPI[]    findAll()
 * @method ProviderAPI[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderAPIRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderAPI::class);
    }

    // /**
    //  * @return ProviderAPI[] Returns an array of ProviderAPI objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProviderAPI
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
