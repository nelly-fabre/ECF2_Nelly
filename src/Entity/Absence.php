<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $absenceDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $absenceDocument = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reason $reason = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbsenceDay(): ?\DateTime
    {
        return $this->absenceDay;
    }

    public function setAbsenceDay(\DateTime $absenceDay): static
    {
        $this->absenceDay = $absenceDay;

        return $this;
    }

    public function getAbsenceDocument(): ?string
    {
        return $this->absenceDocument;
    }

    public function setAbsenceDocument(?string $absenceDocument): static
    {
        $this->absenceDocument = $absenceDocument;

        return $this;
    }

    public function getReason(): ?Reason
    {
        return $this->reason;
    }

    public function setReason(?Reason $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }
}
