<?php

namespace App\Repository;

use App\Entity\CallData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CallData>
 *
 * @method CallData|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallData|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallData[]    findAll()
 * @method CallData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallDataRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManager $entityManager
    )
    {
        parent::__construct($registry, CallData::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CallData $entity, bool $flush = true): void
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
    public function remove(CallData $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function importCSV(string $csvFilePath): void  {
        $connection = $this->entityManager->getConnection();

        $query = sprintf(
            'LOAD DATA LOCAL INFILE %s
            REPLACE INTO TABLE %s 
            FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\'
            (customer_id, datetime, duration, phone_number, ip)',
            $connection->quote($csvFilePath),
            $this->entityManager->getClassMetadata(CallData::class)->getTableName()
        );

        $connection->executeStatement($query);
    }

    // /**
    //  * @return CallData[] Returns an array of CallData objects
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
    public function findOneBySomeField($value): ?CallData
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
