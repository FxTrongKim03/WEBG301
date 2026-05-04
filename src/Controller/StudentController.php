<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Entity\Enrollment;
use App\Form\EnrollmentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student')]
class StudentController extends AbstractController
{
    /**
     * List all students
     */
    #[Route('s', name: 'student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $departmentId = $request->query->get('department', '');

        $query = $studentRepository->createQueryBuilder('s');

        if ($search) {
            $query->where('LOWER(s.firstName) LIKE LOWER(:search)')
                  ->orWhere('LOWER(s.lastName) LIKE LOWER(:search)')
                  ->orWhere('LOWER(s.email) LIKE LOWER(:search)')
                  ->setParameter('search', '%' . $search . '%');
        }

        if ($departmentId) {
            $query->andWhere('s.department = :departmentId')
                  ->setParameter('departmentId', $departmentId);
        }

        $students = $query->getQuery()->getResult();

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'search' => $search,
            'departmentId' => $departmentId,
        ]);
    }

    /**
     * Create a new student
     */
    #[Route('/new', name: 'student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the student to the database
            $entityManager->persist($student);
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', sprintf(
                'Student "%s %s" has been created successfully!',
                $student->getFirstName(),
                $student->getLastName()
            ));

            // Redirect to the student's detail page
            return $this->redirectToRoute('student_show', ['id' => $student->getId()]);
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    /**
     * Show a single student's details
     */
    #[Route('/{id}', name: 'student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        // Prepare an enrollment form pre-filled with this student for the slide-over
        $enrollment = new Enrollment();
        $enrollment->setStudent($student);
        $enrollmentForm = $this->createForm(EnrollmentType::class, $enrollment);

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'enrollment_form' => $enrollmentForm->createView(),
        ]);
    }

    /**
     * Edit an existing student
     */
    #[Route('/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // No need to persist - student is already managed
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Student "%s %s" has been updated successfully!',
                $student->getFirstName(),
                $student->getLastName()
            ));

            return $this->redirectToRoute('student_show', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    /**
     * Delete a student
     */
    #[Route('/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $entityManager): Response
    {
        // CSRF token validation
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $studentName = $student->getFirstName() . ' ' . $student->getLastName();
            
            // Remove the student (enrollments will be cascade deleted)
            $entityManager->remove($student);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Student "%s" has been deleted successfully.',
                $studentName
            ));
        } else {
            $this->addFlash('error', 'Invalid CSRF token. Delete operation failed.');
        }

        return $this->redirectToRoute('student_index');
    }
}