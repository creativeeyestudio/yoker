<?php

namespace App\Repository;

use App\Entity\CodeWeaverFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CodeWeaverFiles>
 *
 * @method CodeWeaverFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeWeaverFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeWeaverFiles[]    findAll()
 * @method CodeWeaverFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeWeaverFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeWeaverFiles::class);
    }

//    /**
//     * @return CodeWeaverFiles[] Returns an array of CodeWeaverFiles objects
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

//    public function findOneBySomeField($value): ?CodeWeaverFiles
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
