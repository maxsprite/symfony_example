<?php

namespace App\Repository;

use App\Entity\RequestCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RequestCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestCall[]    findAll()
 * @method RequestCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestCallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestCall::class);
    }

    // /**
    //  * @return RequestCall[] Returns an array of RequestCall objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RequestCall
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
