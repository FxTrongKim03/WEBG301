<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Department;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Course Name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Data Structures'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Course name is required']),
                    new Assert\Length(['min' => 3, 'max' => 150]),
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Course Code',
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., CS201'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Course code is required']),
                    new Assert\Length(['min' => 3, 'max' => 20]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z0-9]+$/',
                        'message' => 'Code must contain only uppercase letters and numbers',
                    ]),
                ],
            ])
            ->add('credits', IntegerType::class, [
                'label' => 'Credits',
                'attr' => ['class' => 'form-control', 'min' => 1, 'max' => 10],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Credits are required']),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 10,
                        'notInRangeMessage' => 'Credits must be between {{ min }} and {{ max }}',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'required' => false,
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => function (Department $department) {
                    return $department->getName() . ' (' . $department->getCode() . ')';
                },
                'placeholder' => 'Select a department',
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select a department']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}