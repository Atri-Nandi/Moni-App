<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProjectPlanning
{
    #[ORM\Column(type: 'string', length: 36, unique: true), ORM\Id]
    private string $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'projectPlannings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $emp = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /************************* Specific getters/setters **************************/
    
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): ProjectPlanning
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): ProjectPlanning
    {
        $this->description = $description;
        return $this;
    }

    public function getEmp(): ?Employee
    {
        return $this->emp;
    }

    public function setEmp(?Employee $emp): static
    {
        $this->emp = $emp;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): ProjectPlanning
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): ProjectPlanning
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): ProjectPlanning
    {
        $this->notes = $notes;
        return $this;
    }    
}