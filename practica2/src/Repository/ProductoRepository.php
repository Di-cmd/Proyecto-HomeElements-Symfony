<?php

namespace App\Repository;

use App\Entity\Categoria;
use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 *
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    public function add(Producto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Producto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function productosGeneral()

    {
        // EJ 1
        return $this->getEntityManager()
        ->createQuery('SELECT producto.id, producto.nombreP, producto.codigo, producto.cantidad, producto.precio, categoria.nombre
         FROM  App\Entity\Producto AS producto INNER JOIN App\Entity\Categoria AS categoria  
         WITH producto.categoria = categoria.id' )->getResult();

        // EJ2
        // $conn = $this->getEntityManager()->getConnection();
        // $sql = $conn->prepare(
        //     "SELECT producto.id, producto.nombre_p as nombreP, producto.codigo, producto.cantidad, producto.precio, categoria.nombre
        //     FROM producto AS producto
        //         INNER JOIN categoria AS categoria  
        //             ON producto.categoria_id = categoria.id");
        // $resultSet = $sql->executeQuery();
        // return $resultSet->fetchAllAssociative();

        // EJ3
        // return $this->createQueryBuilder('producto')
        //     ->select('producto.id, producto.nombreP, producto.codigo, producto.cantidad, producto.precio, categoria.nombre')
        //     ->from(Producto::class, 'p')
        //     ->innerJoin(Categoria::class, 'categoria', 'WITH', 'producto.categoria = categoria.id')
        //     ->getQuery()
        //     ->getResult();
    }

    //    /**
    //     * @return Producto[] Returns an array of Producto objects
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

    //    public function findOneBySomeField($value): ?Producto
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
