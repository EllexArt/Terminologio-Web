<?php

namespace App\Repository;

use App\Entity\ComposantName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComposantName>
 *
 * @method ComposantName|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComposantName|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComposantName[]    findAll()
 * @method ComposantName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComposantNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComposantName::class);
    }

//    /**
//     * @return ComposantName[] Returns an array of ComposantName objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ComposantName
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
