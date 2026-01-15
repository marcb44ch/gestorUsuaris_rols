<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findBySearchTerm(string $term)
    {
        return $this->createQueryBuilder('p')
            ->where('p.nombre LIKE :term OR p.descripcion LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status)
    {
        return $this->createQueryBuilder('p')
            ->where('p.estado = :status')
            ->setParameter('status', $status)
            ->orderBy('p.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRange(\DateTime $range1, \DateTime $range2)
    {
        return $this->createQueryBuilder('p')
            ->where('p.fecha_inicio >= :range1 AND p.fecha_fin <= :range2')
            ->setParameter('range1', $range1)
            ->setParameter('range2', $range2)
            ->orderBy('p.fecha_inicio', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?string $term, ?string $status, ?\DateTime $range1, ?\DateTime $range2, ?\DateTime $range3, ?\DateTime $range4)
    {
        $qb = $this->createQueryBuilder('p');

        // 1. Filtre per terme (nom o descripció, per exemple)
        if ($term) {
            $qb->andWhere('p.nombre LIKE :term OR p.descripcion LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('p.data_inici', 'DESC');
        }

        // 2. Filtre per estat (Suma la condició a l'anterior)
        if ($status) {
            $qb->andWhere('p.estado = :status')
            ->setParameter('status', $status)
            ->orderBy('p.data_inici', 'DESC');
        }

        // 3. Filtre per rang d'inici
        if ($range1 || $range2) {
            $qb->andWhere('p.fecha_inicio BETWEEN :start AND :end')
            ->setParameter('start', $range1 ?: new \DateTime('0001-01-01'))
            ->setParameter('end',   $range2 ?: new \DateTime('9999-12-31'))
            ->orderBy('p.data_inici', 'DESC');
        }

        // 4. Filtre per rang d'acabada
        if ($range3 || $range4) {
            $qb->andWhere('p.fecha_fin BETWEEN :start AND :end')
            ->setParameter('start', $range3 ?: new \DateTime('0001-01-01'))
            ->setParameter('end',   $range4 ?: new \DateTime('9999-12-31'))
            ->orderBy('p.data_inici', 'DESC');
        }

        $qb->orderBy('p.dataInici', 'DESC');

        return $qb->orderBy('p.nombre', 'ASC')
                ->getQuery()
                ->getResult();
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
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

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
