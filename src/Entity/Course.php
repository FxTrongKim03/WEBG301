<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Course name is required')]
    #[Assert\Length(min: 3, max: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank(message: 'Course code is required')]
    #[Assert\Regex(pattern: '/^[A-Z0-9]+$/', message: 'Code must contain only uppercase letters and numbers')]
    private ?string $code = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', options: ['default' => 3])]
    #[Assert\NotBlank(message: 'Credits are required')]
    #[Assert\Range(min: 1, max: 10, notInRangeMessage: 'Credits must be a number between {{ min }} and {{ max }}')]
    private int $credits = 3;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\OneToMany(targetEntity: Enrollment::class, mappedBy: 'course', cascade: ['persist', 'remove'])]
    private Collection $enrollments;

    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getCredits(): int { return $this->credits; }
    public function setCredits(int $credits): static { $this->credits = $credits; return $this; }

    public function getDepartment(): ?Department { return $this->department; }
    public function setDepartment(?Department $department): static { $this->department = $department; return $this; }

    public function getEnrollments(): Collection { return $this->enrollments; }
}