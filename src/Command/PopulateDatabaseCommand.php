<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\Department;
use App\Entity\Enrollment;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:populate-database',
    description: 'Populate the database with sample data',
)]
class PopulateDatabaseCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Populating Database with Sample Data');

        // Departments
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CS', 'description' => 'Study of computation and computer systems'],
            ['name' => 'Business Administration', 'code' => 'BA', 'description' => 'Management and business operations'],
            ['name' => 'Engineering', 'code' => 'ENG', 'description' => 'Applied science and mathematics'],
            ['name' => 'Arts and Humanities', 'code' => 'AH', 'description' => 'Literature, arts, and philosophy'],
        ];

        $departmentEntities = [];
        foreach ($departments as $deptData) {
            $department = new Department();
            $department->setName($deptData['name']);
            $department->setCode($deptData['code']);
            $department->setDescription($deptData['description']);
            $this->entityManager->persist($department);
            $departmentEntities[] = $department;
        }
        $this->entityManager->flush();
        $io->success(sprintf('Created %d departments', count($departments)));

        // Students
        $students = [
            ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'john.doe@example.com', 'dob' => '2000-05-15'],
            ['firstName' => 'Jane', 'lastName' => 'Smith', 'email' => 'jane.smith@example.com', 'dob' => '1999-08-22'],
            ['firstName' => 'Michael', 'lastName' => 'Johnson', 'email' => 'michael.j@example.com', 'dob' => '2001-03-10'],
            ['firstName' => 'Emily', 'lastName' => 'Brown', 'email' => 'emily.brown@example.com', 'dob' => '2000-11-30'],
            ['firstName' => 'David', 'lastName' => 'Wilson', 'email' => 'david.w@example.com', 'dob' => '1998-07-18'],
        ];

        $studentEntities = [];
        foreach ($students as $stud) {
            $student = new Student();
            $student->setFirstName($stud['firstName']);
            $student->setLastName($stud['lastName']);
            $student->setEmail($stud['email']);
            $student->setDateOfBirth(new \DateTime($stud['dob']));
            $student->setDepartment($departmentEntities[array_rand($departmentEntities)]);
            $this->entityManager->persist($student);
            $studentEntities[] = $student;
        }
        $this->entityManager->flush();
        $io->success(sprintf('Created %d students', count($students)));

        // Courses
        $courses = [
            ['name' => 'Data Structures', 'code' => 'CS201', 'credits' => 4],
            ['name' => 'Database Systems', 'code' => 'CS301', 'credits' => 3],
            ['name' => 'Marketing Principles', 'code' => 'BA101', 'credits' => 3],
            ['name' => 'Financial Accounting', 'code' => 'BA202', 'credits' => 4],
            ['name' => 'Mechanical Engineering', 'code' => 'ENG101', 'credits' => 5],
        ];

        $courseEntities = [];
        foreach ($courses as $crs) {
            $course = new Course();
            $course->setName($crs['name']);
            $course->setCode($crs['code']);
            $course->setCredits($crs['credits']);
            $course->setDescription('Detailed description for ' . $crs['name']);
            $course->setDepartment($departmentEntities[array_rand($departmentEntities)]);
            $this->entityManager->persist($course);
            $courseEntities[] = $course;
        }
        $this->entityManager->flush();
        $io->success(sprintf('Created %d courses', count($courses)));

        // Enrollments
        for ($i = 0; $i < 10; $i++) {
            $enrollment = new Enrollment();
            $enrollment->setStudent($studentEntities[array_rand($studentEntities)]);
            $enrollment->setCourse($courseEntities[array_rand($courseEntities)]);
            $status = (rand(0, 1) === 1) ? Enrollment::STATUS_COMPLETED : Enrollment::STATUS_ACTIVE;
            $enrollment->setStatus($status);

            if ($status === Enrollment::STATUS_COMPLETED) {
                $enrollment->setGrade(rand(60, 100));
            }

            $this->entityManager->persist($enrollment);
        }
        $this->entityManager->flush();
        $io->success('Created 10 enrollments');

        $io->success('Database populated successfully!');

        return Command::SUCCESS;
    }
}
