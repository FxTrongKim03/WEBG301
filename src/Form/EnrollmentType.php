<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EnrollmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function (Student $student) {
                    return $student->getFirstName() . ' ' . $student->getLastName() . ' (' . $student->getEmail() . ')';
                },
                'placeholder' => 'Select a student',
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select a student']),
                ],
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'choice_label' => function (Course $course) {
                    return $course->getName() . ' (' . $course->getCode() . ')';
                },
                'placeholder' => 'Select a course',
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select a course']),
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Active' => 'active',
                    'Completed' => 'completed',
                    'Dropped' => 'dropped',
                    'Withdrawn' => 'withdrawn',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('grade', NumberType::class, [
                'label' => 'Grade',
                'attr' => ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'max' => '100'],
                'required' => false,
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Grade must be between {{ min }} and {{ max }}',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enrollment::class,
        ]);
    }
}