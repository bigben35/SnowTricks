<?php

namespace App\Repository;

use App\Entity\Illustration;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Illustration>
 *
 * @method Illustration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Illustration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Illustration[]    findAll()
 * @method Illustration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IllustrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Illustration::class);
    }

    public function save(Illustration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Illustration $entity, bool $flush = false, $imageDirectory = null): void
    {
        $this->getEntityManager()->remove($entity);
        $filesystem = new Filesystem();
        $filesystem->remove($imageDirectory . "/" . $entity->getFile()); // Supprimer l'image du répertoire

        if ($flush) {

            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Illustration[] Returns an array of Illustration objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Illustration
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}