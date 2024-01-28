<?php

namespace App\Repository;

use App\Entity\CallData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\File;

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
    public function __construct(ManagerRegistry $registry)
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

    public function importCSV(File $csvFile): void  {
        $path = $csvFile->getPathname();

        // Get the EntityManager connection
        $connection = $this->getEntityManager()->getConnection();

        // // Create a query
        // $query = $entityManager->createQuery('
        //     LOAD DATA LOCAL INFILE :path INTO TABLE :table
        //     FIELDS TERMINATED BY ','
        //     ENCLOSED BY \'"\'
        //     LINES TERMINATED BY \'\r\n\'
        //     IGNORE 1 LINES
        //     (col1, col2, col3, col4, col5...);
        // ');

        // // Set parameters if needed
        // $query->setParameter('path', $path);
        // $query->setParameter('table', $this->getEntityName());

        // echo $query->getSQL();

        $query = sprintf(
            'LOAD DATA LOCAL INFILE %s
            INTO TABLE %s 
            FIELDS TERMINATED BY \',\' ENCLOSED BY \'"\' LINES TERMINATED BY \'\\n\'
            (customer_id, datetime, duration, phone_number, ip)',
            $connection->quote($path),
            'CallData'
        );

        echo $query;

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
