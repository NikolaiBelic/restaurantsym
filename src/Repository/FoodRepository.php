<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Food>
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    //    /**
    //     * @return Food[] Returns an array of Food objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Food
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function remove(Food $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Food[] Returns an array of Imagen objects
     */
    public function findLikeNombre(string $value): array
    {
        $qb = $this->createQueryBuilder('i');
        $qb->Where($qb->expr()->like('i.nombre', ':val'))->setParameter('val', '%' . $value . '%');
        return $qb->getQuery()->getResult();
    }

    public function findFoodConCategoria(string $ordenacion, string $tipoOrdenacion)
    {
        $qb = $this->createQueryBuilder('food');
        $qb->addSelect('categoria')
            ->innerJoin('food.categoria', 'categoria')
            ->orderBy('food.' . $ordenacion, $tipoOrdenacion);
        return $qb->getQuery()->getResult();
    }
}
