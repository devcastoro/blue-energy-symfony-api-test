<?php

namespace App\Repository;

use App\Entity\MeterReads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MeterReads|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeterReads|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeterReads[]    findAll()
 * @method MeterReads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method MeterReads[]    findByCustomerId(string $customerId)
 */
class MeterReadsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MeterReads::class);
    }

    /**
     * @return MeterReads[] Returns an array of MeterReads objects
     */
    public function findAll()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return MeterReads[] Returns an array of MeterReads objects
     */
    public function findAllByMeterId($meterId)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.meter = :meterId')
            ->setParameter('meterId', $meterId)
            ->orderBy('m.readDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
