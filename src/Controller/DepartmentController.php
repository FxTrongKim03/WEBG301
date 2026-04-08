<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/department')]
class DepartmentController extends AbstractController
{
    #[Route('s', name: 'department_index', methods: ['GET'])]
    public function index(DepartmentRepository $departmentRepository): Response
    {
        return $this->render('department/index.html.twig', [
            'departments' => $departmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'department_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Department "%s" has been created successfully!',
                $department->getName()
            ));

            return $this->redirectToRoute('department_show', ['id' => $department->getId()]);
        }

        return $this->render('department/new.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'department_show', methods: ['GET'])]
    public function show(Department $department): Response
    {
        return $this->render('department/show.html.twig', [
            'department' => $department,
        ]);
    }

    #[Route('/{id}/edit', name: 'department_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Department "%s" has been updated successfully!',
                $department->getName()
            ));

            return $this->redirectToRoute('department_show', ['id' => $department->getId()]);
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'department_delete', methods: ['POST'])]
    public function delete(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            // Check if department has students or courses
            if ($department->getStudents()->count() > 0 || $department->getCourses()->count() > 0) {
                $this->addFlash('error', sprintf(
                    'Cannot delete department "%s" because it has associated students or courses. Please reassign them first.',
                    $department->getName()
                ));
                return $this->redirectToRoute('department_show', ['id' => $department->getId()]);
            }

            $departmentName = $department->getName();
            $entityManager->remove($department);
            $entityManager->flush();

            $this->addFlash('success', sprintf(
                'Department "%s" has been deleted successfully.',
                $departmentName
            ));
        }

        return $this->redirectToRoute('department_index');
    }
}