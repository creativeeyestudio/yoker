<?php

namespace App\Repository;

use App\Entity\SocialManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SocialManager>
 *
 * @method SocialManager|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialManager|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialManager[]    findAll()
 * @method SocialManager[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialManagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialManager::class);
    }

//    /**
//     * @return SocialManager[] Returns an array of SocialManager objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SocialManager
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
