<?php

namespace App\Repository;

use App\Entity\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Result>
 *
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function save(Result $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Result $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Result[] Returns an array of Result objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.race = :id')
           ->setParameter('id', $value)
           ->orderBy('r.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
   public function findByRaceIdAndDistance($value,$distance): array
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.race = :id')
           ->andWhere('r.distance = :distance')
           ->setParameter('id', $value)
           ->setParameter('distance', $distance)
           ->orderBy('r.RaceTime', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
   public function findByRaceID($value): array
   {
       return $this->createQueryBuilder('r')
           ->andWhere('r.race = :id')
           ->setParameter('id', $value)
           ->orderBy('r.RaceTime', 'ASC')
           ->orderBy('r.distance', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
//    public function findOneBySomeField($value): ?Result
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
