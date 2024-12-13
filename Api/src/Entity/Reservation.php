<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    #[ORM\ManyToOne(inversedBy: 'reservation')]
    private ?User $relation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timeSlotStart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timeSlotEnd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function getRelation(): ?User
    {
        return $this->relation;
    }

    public function setRelation(?User $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getTimeSlotStart(): ?\DateTimeInterface
    {
        return $this->timeSlotStart;
    }

    public function setTimeSlotStart(\DateTimeInterface $timeSlotStart): static
    {
        $this->timeSlotStart = $timeSlotStart;

        return $this;
    }

    public function getTimeSlotEnd(): ?\DateTimeInterface
    {
        return $this->timeSlotEnd;
    }

    public function setTimeSlotEnd(\DateTimeInterface $timeSlotEnd): static
    {
        $this->timeSlotEnd = $timeSlotEnd;

        return $this;
    }
}
