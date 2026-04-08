<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'This department code is already in use')]

class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 10)]
    #[Assert\Regex(pattern: '/^[A-Z0-9]+$/', message: 'Code must contain only uppercase letters and numbers')]
    private ?string $code = null;

    // Inverse side of the relationships (populated by Doctrine)
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'department')]
    private Collection $students;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'department')]
    private Collection $courses;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->courses  = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getStudents(): Collection { return $this->students; }
    public function getCourses(): Collection { return $this->courses; }
}