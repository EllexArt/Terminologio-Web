<?php

namespace App\Repository;

use App\Entity\ComponentName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComponentName>
 *
 * @method ComponentName|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentName|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComponentName[]    findAll()
 * @method ComponentName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComponentName::class);
    }

    public function getComponentNameFromComponentAndLanguage(Component $component, Language $language) : ?ComponentName
    {
        $componentsName = $component->getComponentNames();
        foreach ($componentsName as $componentName) {
            if($componentName->getLanguage()->getName() == $language->getName()) {
                return $componentName;
            }
        }
        return null;
    }

//    /**
//     * @return ComponentName[] Returns an array of ComponentName objects
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

    /**
     * @return ComponentName[] Returns an array of ComponentName objects
     *          by language value
     */
     public function findByLanguage($value): array
     {
        return $this->createQueryBuilder('c')
        ->andWhere('c.language = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?ComponentName
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
