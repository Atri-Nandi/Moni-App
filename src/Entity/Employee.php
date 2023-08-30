<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'Employees')]
class Employee
{
    #[ORM\Column(type: 'string', length: 36, unique: true), ORM\Id]
    private string $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'emp', targetEntity: ProjectPlanning::class)]
    private Collection $projectPlannings;

    /************************* Public Functions **************************/

    public function __construct()
    {
        $this->projectPlannings = new ArrayCollection();
    }

    /**
     * Returns the fullname with firstname lastname and email
     * 
     * @return string Full Name
     */
    public function getFullName(): string
    {
        $username = trim(str_replace(' ', ' ', $this->getFirstName().' '.$this->getLastName().' <'.$this->getEmail().'>'));

        return $username;        
    }

    /************************* Specific getters/setters **************************/


    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): Employee
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): Employee
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): Employee
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Employee
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Collection<int, ProjectPlanning>
     */
    public function getProjectPlannings(): Collection
    {
        return $this->projectPlannings;
    }

    public function addProjectPlanning(ProjectPlanning $projectPlanning): static
    {
        if (!$this->projectPlannings->contains($projectPlanning)) {
            $this->projectPlannings->add($projectPlanning);
            $projectPlanning->setEmp($this);
        }

        return $this;
    }

    public function removeProjectPlanning(ProjectPlanning $projectPlanning): static
    {
        if ($this->projectPlannings->removeElement($projectPlanning)) {
            // set the owning side to null (unless already changed)
            if ($projectPlanning->getEmp() === $this) {
                $projectPlanning->setEmp(null);
            }
        }

        return $this;
    }

}