<?php

namespace App\Repository;

use App\Entity\Casquette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Casquette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casquette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casquette[]    findAll()
 * @method Casquette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasquetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casquette::class);
    }

    // /**
    //  * @return Casquette[] Returns an array of Casquette objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Casquette
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
