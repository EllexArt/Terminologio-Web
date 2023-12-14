<?php

namespace App\Repository;

use App\Entity\Concept;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Concept>
 *
 * @method Concept|null find($id, $lockMode = null, $lockVersion = null)
 * @method Concept|null findOneBy(array $criteria, array $orderBy = null)
 * @method Concept[]    findAll()
 * @method Concept[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConceptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Concept::class);
    }

//    /**
//     * @return Concept[] Returns an array of Concept objects
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

//    public function findOneBySomeField($value): ?Concept
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
