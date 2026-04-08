<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Form\EnrollmentType;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/enrollment')]
class EnrollmentController extends AbstractController
{
    #[Route('s', name: 'enrollment_index', methods: ['GET'])]
    public function index(EnrollmentRepository $enrollmentRepository): Response
    {
        return $this->render('enrollment/index.html.twig', [
            'enrollments' => $enrollmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'enrollment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $enrollment = new Enrollment();
        $form = $this->createForm(EnrollmentType::class, $enrollment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if student is already enrolled in this course
            $existingEnrollment = $entityManager->getRepository(Enrollment::class)->findOneBy([
                'student' => $enrollment->getStudent(),
                'course' => $enrollment->getCourse(),
            ]);

            if ($existingEnrollment) {
                $this->addFlash('error', sprintf(
                    'Student "%s %s" is already enrolled in course "%s".',
                    $enrollment->getStudent()->getFirstName(),
                    $enrollment->getStudent()->getLastName(),
                    $enrollment->getCourse()->getName()
                ));

                return $this->render('enrollment/new.html.twig', [
                    'enrollment' => $enrollment,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($enrollment);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Student "%s %s" has been enrolled in "%s" successfully!',
                $enrollment->getStudent()->getFirstName(),
                $enrollment->getStudent()->getLastName(),
                $enrollment->getCourse()->getName()
            ));

            return $this->redirectToRoute('enrollment_show', ['id' => $enrollment->getId()]);
        }

        return $this->render('enrollment/new.html.twig', [
            'enrollment' => $enrollment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'enrollment_show', methods: ['GET'])]
    public function show(Enrollment $enrollment): Response
    {
        return $this->render('enrollment/show.html.twig', [
            'enrollment' => $enrollment,
        ]);
    }

    #[Route('/{id}/edit', name: 'enrollment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enrollment $enrollment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnrollmentType::class, $enrollment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Enrollment has been updated successfully!');

            return $this->redirectToRoute('enrollment_show', ['id' => $enrollment->getId()]);
        }

        return $this->render('enrollment/edit.html.twig', [
            'enrollment' => $enrollment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'enrollment_delete', methods: ['POST'])]
    public function delete(Request $request, Enrollment $enrollment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enrollment->getId(), $request->request->get('_token'))) {
            $studentName = $enrollment->getStudent()->getFirstName() . ' ' . $enrollment->getStudent()->getLastName();
            $courseName = $enrollment->getCourse()->getName();

            $entityManager->remove($enrollment);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Enrollment of "%s" in "%s" has been deleted successfully.',
                $studentName,
                $courseName
            ));
        }

        return $this->redirectToRoute('enrollment_index');
    }
}