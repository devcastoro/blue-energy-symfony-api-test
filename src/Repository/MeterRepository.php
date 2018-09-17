<?php

namespace App\Repository;

use App\Entity\Meter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Meter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meter[]    findAll()
 * @method Meter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeterRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Meter::class);
    }

    /**
     * @return Meter[] Returns an array of Meter objects
     */
    public function findOneByMpxn($mpxn)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.mpxn = :mpxn')
            ->setParameter('mpxn', $mpxn)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    /**
     * @return Meter[] Returns an array of Meter objects
     */
    public function findOneByMpxnAndCustomerId($mpxn,$customerId)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.mpxn = :mpxn')
            ->andWhere('m.customer = :customer')
            ->setParameter('mpxn', $mpxn)
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
