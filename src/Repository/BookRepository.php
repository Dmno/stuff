<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Book::class);
        $this->paginator = $paginator;
    }

    /**
     * @param string|null $term
     * @return Books[]
     */
    public function findAllBooksWithSearchQuery(?string $term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        if ($term) {
            $qb->andWhere('c.title LIKE :term OR c.author LIKE :term OR c.genre LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }

        return $qb
            ->orderBy('c.id', 'DESC');
            ;
    }

//    /**
//     * @param string|null $term
//     * @return Books[]
//     */
//    public function findAllBooksByGenre(?string $genre): QueryBuilder
//    {
//        $qy = $this->createQueryBuilder('h');
//
//        if ($genre) {
//            $qy->andWhere('h.genre = :genre')
//                ->setParameter('genre', '%' . $genre . '%');
//        }
//
//        return $qy
//            ->orderBy('h.id', 'ASC');
//        ;
//    }

//    /**
//     * @param string|null $term
//     * @return Books[]
//     */
//    public function findAllBooksByGenre(?string $genre): QueryBuilder
//    {
//        $qb = $this->createQueryBuilder('l')
//            ->andWhere('l.genre = :genre')
//            ->setParameter('genre', $genre);
//
//        return $qb
//            ->orderBy('l.id', 'DESC');
//        ;
//    }

//    public function findAllWithSearch(?string $term)
//    {
//        $qb = $this->createQueryBuilder('c');
//
//        if ($term) {
//            $qb->andWhere('c.title LIKE :term OR c.author LIKE :term OR c.genre LIKE :term')
//                ->setParameter('term', '%' . $term . '%');
//        }
//
//        return $qb
//            ->getQuery()
//            ->getResult()
//            ;
//    }


    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
