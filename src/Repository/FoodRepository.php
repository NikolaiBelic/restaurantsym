<?php

namespace App\Repository;

use DateTime;
use App\Entity\Food;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
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
    public function findFood(string $nombre, string $fechaInicial, $fechaFinal, User $usuario): array
    {
        $qb = $this->createQueryBuilder('food');
        if (!is_null($nombre) && $nombre !== '') {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('food.descripcion', ':val'),
                    $qb->expr()->like('food.nombre', ':val')
                )
            )
                ->setParameter('val', '%' . $nombre . '%');
        }
        if (!is_null($fechaInicial) && $fechaInicial !== '') {
            $dtFechaInicial = DateTime::createFromFormat('Y-m-d', $fechaInicial);
            $qb->andWhere($qb->expr()->gte('food.fecha', ':fechaInicial'))
                ->setParameter('fechaInicial', $dtFechaInicial);
        }
        if (!is_null($fechaFinal) && $fechaFinal !== '') {
            $dtFechaFinal = DateTime::createFromFormat('Y-m-d', $fechaFinal);
            $qb->andWhere($qb->expr()->lte('food.fecha', ':fechaFinal'))
                ->setParameter('fechaFinal', $dtFechaFinal);
        }

        $this->addUserFilter($qb, $usuario);

        return $qb->getQuery()->getResult();
    }

    public function findFoodConCategoria(string $ordenacion, string $tipoOrdenacion, User $usuario)
    {
        $qb = $this->createQueryBuilder('food');
        $qb->addSelect('categoria')
            ->innerJoin('food.categoria', 'categoria')
            ->orderBy('food.' . $ordenacion, $tipoOrdenacion);
        $this->addUserFilter($qb, $usuario);
        return $qb->getQuery()->getResult();
    }

    private function addUserFilter(QueryBuilder $qb, User $usuario)
    {
        // Si no es administrador se aplica el filtro.
        // En caso contrario, no se aplica ningún filtro
        if (in_array('ROLE_ADMIN', $usuario->getRoles()) === false) {
            $qb->innerJoin('food.usuario', 'usuario')
                ->andWhere($qb->expr()->eq('food.usuario', ':usuario'))
                ->setParameter('usuario', $usuario);
        }
    }
}
