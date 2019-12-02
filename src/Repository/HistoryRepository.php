<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

//    /**
//     * @param string|null $term
//     * @return History[]
//     */
//    public function findAllWithSearch(?string $s)
//    {
//        $qp = $this->createQueryBuilder('g');
//
//        if ($s) {
//            $qp->andWhere('g.user LIKE :s OR g.book LIKE :s ')
//                ->setParameter('s', '%' . $s . '%');
//        }
//
//        return $qp
//            ->getQuery()
//            ->getResult()
//            ;
//    }

    /**
     * @param string|null $term
     * @return History[]
     */
    public function findAllWithSearchQueryBuilder(?string $s): QueryBuilder
    {
        $qp = $this->createQueryBuilder('g');

        if ($s) {
            $qp->andWhere('g.user LIKE :s OR g.book LIKE :s ')
                ->setParameter('s', '%' . $s . '%');
        }

        return $qp
            ->orderBy('g.user', 'DESC');
            ;
    }

    /**
     * @param string|null $term
     * @return History[]
     */
    public function findAllByUser(?string $d): QueryBuilder
    {
        $qi = $this->createQueryBuilder('j');

        if ($d) {
            $qi->andWhere('j.user = :j')
                ->setParameter('d', '%' . $d . '%');
        }
    }

    // /**
    //  * @return History[] Returns an array of History objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
