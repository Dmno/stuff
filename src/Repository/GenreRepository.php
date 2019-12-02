<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    /**
     * @param string|null $term
     * @return Genre[]
     */
    public function findAllGenres(?string $gen): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f');

        if ($gen) {
            $qb->andWhere('f.title LIKE :gen')
                ->setParameter('gen', '%' . $gen . '%');
        }

        return $qb
            ->orderBy('f.id', 'ASC');
        ;
    }

    public function getAllGenres()
    {
        return $this->createQueryBuilder('b')
            ->select('b.id', 'b.title')
            ->getQuery()
            ->getResult();
    }


}
