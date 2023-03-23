<?php

namespace App\Repository;

use App\Entity\CommentTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    public function findCommentsPaginated(int $page, string $slug, int $limit = 4): array 
    {
        $limit = abs($limit);

        $result = [];

        // récupération des infos  
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('t', 'c')
            ->from('App\Entity\CommentTrick', 'c')
            ->join('c.trick', 't')
            ->where("t.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        // on vérifie qu'il ya  des données 
        if(empty($data)){
            return $result;
        }

        // calcul nb pages 
        $pages = ceil($paginator->count() / $limit);

        //on remplit tableau 
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

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
