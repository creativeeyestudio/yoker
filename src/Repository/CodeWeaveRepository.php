<?php

namespace App\Repository;

use App\Entity\CodeWeave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CodeWeave>
 *
 * @method CodeWeave|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeWeave|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeWeave[]    findAll()
 * @method CodeWeave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeWeaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeWeave::class);
    }

//    /**
//     * @return CodeWeave[] Returns an array of CodeWeave objects
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

//    public function findOneBySomeField($value): ?CodeWeave
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
