<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[Gedmo\SoftDeleteable(fieldName:"deletedAt", timeAware:false, hardDelete:false)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property()]
    #[Groups("default")]
    private ?int $id;

    #[OA\Property()]
    #[Groups("default")]
    #[ORM\Column(length: 100)]
    private ?string $name;

    #[OA\Property()]
    #[Groups("default")]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description;

    #[OA\Property(format: "date-time")]
    #[Groups("default")]
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $startDate;

    #[OA\Property(format: "date-time")]
    #[Groups("default")]
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $endDate;

    #[OA\Property()]
    #[Groups("default")]
    #[ORM\Column]
    private ?bool $done;

    #[OAT\Property(ref: "#/components/schemas/user")]
    #[Groups("default")]
    #[ORM\ManyToOne(inversedBy: 'tasks', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): static
    {
        $this->done = $done;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
