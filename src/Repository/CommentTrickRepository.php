<?php

namespace App\Repository;

use App\Entity\CommentTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentTrick>
 *
 * @method CommentTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentTrick[]    findAll()
 * @method CommentTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentTrick::class);
    }

    public function save(CommentTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CommentTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    // pagination 
    // public function findCommentsPaginated(int $page, string $slug, int $limit = 4): array 
    // {
    //     $limit = abs($limit);

    //     $result = [];

    //     $query = $this->getEntityManager()->createQueryBuilder()->select('c')->from('App\Entity\CommentTrick', 'c')->where("");

    //     dd($query->getQuery()->getResult());

    //     return $result;
    // }

//    /**
//     * @return CommentTrick[] Returns an array of CommentTrick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CommentTrick
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
