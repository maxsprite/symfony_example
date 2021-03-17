<?php

namespace App\Repository;

use App\Entity\HashTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HashTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method HashTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method HashTag[]    findAll()
 * @method HashTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HashTag::class);
    }

    // /**
    //  * @return HashTag[] Returns an array of HashTag objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HashTag
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
