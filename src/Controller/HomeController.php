<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use App\Repository\DepartmentRepository;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        StudentRepository $studentRepo,
        DepartmentRepository $departmentRepo,
        CourseRepository $courseRepo,
        EnrollmentRepository $enrollmentRepo
    ): Response
    {
        $totalStudents = $studentRepo->count([]);
        $totalDepartments = $departmentRepo->count([]);
        $totalCourses = $courseRepo->count([]);
        $totalEnrollments = $enrollmentRepo->count([]);

        $stats = [
            'totalStudents' => $totalStudents,
            'totalDepartments' => $totalDepartments,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
        ];

        return $this->render('home/index.html.twig', ['stats' => $stats]);
    }

    #[Route('/about', name: 'home_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }
}