<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Return the total number of Post objects
     *
     * @return int
     **/
    public function countObjects(): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return a paginated array of Post objects
     *
     * @param int $limit  The max number of objects to return
     * @param int $offset The number of objects to skip
     *
     * @return array
     **/
    public function getObjectsPaginated(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return the total number of Post objects matching the given search string
     *
     * @param string $needle The search string
     *
     * @return int
     **/
    public function countFiltered(string $needle): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.author', 'u')
            ->andWhere('p.content LIKE :needle')
            ->orWhere('u.email LIKE :needle')
            ->setParameter('needle', "%{$needle}%")
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return a paginated array of Post objects matching the given search string
     *
     * @param string $needle The search string
     * @param int    $limit  The max number of objects to return
     * @param int    $offset The number of objects to skip
     *
     * @return array
     **/
    public function getFilteredPaginated(
        string $needle,
        int $limit,
        int $offset
    ): array {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->innerJoin('p.author', 'u')
            ->andWhere('p.content LIKE :needle')
            ->orWhere('u.email LIKE :needle')
            ->setParameter('needle', "%{$needle}%")
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return the total number of Post objects matching a given set of
     * filters.
     *
     * @param array $needles The filter(s)
     *
     * @return int
     **/
    public function countSearched(array $needles): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.author', 'u');

        if (isset($needles['content'])) {
            $content = $needles['content'];
            $qb->andWhere('p.content LIKE :needle')
                ->setParameter('needle', "%{$content}%");
        }

        if (isset($needles['author'])) {
            $author = trim(strtolower($needles['author']));
            $qb->andWhere('u.email = :needle')
                ->setParameter('needle', $author);
        }

        return $qb->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return a paginated array of Post objects matching a given set of filters.
     *
     * @param array $needles The filter(s)
     * @param int   $limit   The max number of objects to return
     * @param int   $offset  The number of objects to skip
     *
     * @return array
     **/
    public function getSearchedPaginated(
        array $needles,
        int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->innerJoin('p.author', 'u');

        if (isset($needles['content'])) {
            $content = $needles['content'];
            $qb->andWhere('p.content LIKE :needle')
                ->setParameter('needle', "%{$content}%");
        }

        if (isset($needles['author'])) {
            $author = trim(strtolower($needles['author']));
            $qb->andWhere('u.email = :needle')
                ->setParameter('needle', $author);
        }

        return $qb->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
