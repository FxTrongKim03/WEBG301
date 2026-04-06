<?php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    // All enrollments for one student — used on student show page
    public function findByStudent(int $studentId): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.course', 'c')
            ->addSelect('c')
            ->andWhere('e.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->orderBy('e.enrolledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // All enrollments for one course — used on course show page
    public function findByCourse(int $courseId): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.student', 's')
            ->addSelect('s')
            ->andWhere('e.course = :courseId')
            ->setParameter('courseId', $courseId)
            ->orderBy('s.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
