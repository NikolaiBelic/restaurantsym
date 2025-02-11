<?php

namespace App\Repository;

use DateTime;
use App\Entity\Food;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function findFood(string $nombre, string $fechaInicial, $fechaFinal): array
    {
        $qb = $this->createQueryBuilder('i');
        if (!is_null($nombre) && $nombre !== '') {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('i.descripcion', ':val'),
                    $qb->expr()->like('i.nombre', ':val')
                )
            )
                ->setParameter('val', '%' . $nombre . '%');
        }
        if (!is_null($fechaInicial) && $fechaInicial !== '') {
            $dtFechaInicial = DateTime::createFromFormat('Y-m-d', $fechaInicial);
            $qb->andWhere($qb->expr()->gte('i.fecha', ':fechaInicial'))
                ->setParameter('fechaInicial', $dtFechaInicial);
        }
        if (!is_null($fechaFinal) && $fechaFinal !== '') {
            $dtFechaFinal = DateTime::createFromFormat('Y-m-d', $fechaFinal);
            $qb->andWhere($qb->expr()->lte('i.fecha', ':fechaFinal'))
                ->setParameter('fechaFinal', $dtFechaFinal);
        }
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
