<?php

namespace App\Repository;

use App\Entity\RequestTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RequestTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestTransaction[]    findAll()
 * @method RequestTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestTransaction::class);
    }

    // /**
    //  * @return RequestTransaction[] Returns an array of RequestTransaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RequestTransaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
