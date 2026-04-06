<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/students', name: 'app_student_index')]
    public function index(StudentRepository $studentRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('q', '');

        $students = ($searchTerm !== '')
            ? $studentRepository->findByName($searchTerm)
            : $studentRepository->findAll();

        return $this->render('student/index.html.twig', [
            'page_title'  => 'Student List',
            'students'    => $students,
            'search_term' => $searchTerm,
        ]);
    }
    
    #[Route('/students_new', name: 'app_student_new')]
    public function new(): Response
    {
        return  $this->render('student/new.html.twig', [
            'page_title' => 'Add New Student',
        ]);
    }

    #[Route('/students/{id}', name: 'app_student_show')]
    public function show(StudentRepository $studentRepository, int $id): Response
    {
        $student = $studentRepository->find($id);  

        return $this->render('student/show.html.twig', [
            'page_title' => 'Student Details',
            'student'    => $student,
        ]);
    }

    #[Route('/students/{id}/edit', name: 'app_student_edit')]
    public function edit(StudentRepository $studentRepository, int $id): Response
    {
        $student = $studentRepository->find($id);

        return $this->render('student/edit.html.twig', [
            'page_title' => 'Edit Student',
            'student'    => $student,
        ]);
    }

}