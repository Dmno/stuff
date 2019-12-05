<?php

namespace App\Repository;

use App\Entity\Subs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Subs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subs[]    findAll()
 * @method Subs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubsRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Subs::class);
        $this->em = $entityManager;
    }

    public function incrementAmount(Subs $subs) :QueryBuilder
    {
        $qb = $this->createQueryBuilder('h')
                ->update(Subs::class, 's')
                ->set('s.amount', $subs->getAmount() + 1);

        $query = $qb->getQuery();

    return $query->execute();
    }


    // /**
    //  * @return Subs[] Returns an array of Subs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subs
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
