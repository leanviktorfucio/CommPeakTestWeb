<?php

namespace App\Repository;

use App\Entity\CallDataStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CallDataStatistic>
 *
 * @method CallDataStatistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallDataStatistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallDataStatistic[]    findAll()
 * @method CallDataStatistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallDataStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallDataStatistics::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CallDataStatistics $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(CallDataStatistics $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return CallDataStatistic[] Returns an array of CallDataStatistic objects
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
    public function findOneBySomeField($value): ?CallDataStatistic
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
