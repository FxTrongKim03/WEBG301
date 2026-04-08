<?php

namespace App\Form;

use App\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Department Name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Computer Science'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Department name is required']),
                    new Assert\Length(['min' => 3, 'max' => 100]),
                ],
            ])
            ->add('code', TextType::class, [
                'label' => 'Department Code',
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., CS'],
                'help' => 'Short code (2-10 characters)',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Department code is required']),
                    new Assert\Length(['min' => 2, 'max' => 10]),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z0-9]+$/',
                        'message' => 'Code must contain only uppercase letters and numbers',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the department...'],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}