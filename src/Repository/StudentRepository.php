<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    // Search by partial name — used for the student search page (F1)
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.firstName LIKE :name OR s.lastName LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('s.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // List all students in one department — used on Department detail page (F2)
    public function findByDepartment(int $departmentId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.department = :deptId')
            ->setParameter('deptId', $departmentId)
            ->orderBy('s.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Fetch one student with enrollments pre-loaded — avoids N+1 queries on show page
    public function findWithEnrollments(int $id): ?Student
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.enrollments', 'e')
            ->leftJoin('e.course', 'c')
            ->addSelect('e', 'c')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}