<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="inscriptions")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=circuit::class, inversedBy="inscriptions")
     */
    private $circuit;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCourse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?user
    {
        return $this->user;
    }

    public function setUserId(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCircuitId(): ?circuit
    {
        return $this->circuit;
    }

    public function setCircuitId(?circuit $circuit): self
    {
        $this->circuit = $circuit;

        return $this;
    }

    public function getDateCourse(): ?\DateTimeInterface
    {
        return $this->dateCourse;
    }

    public function setDateCourse(\DateTimeInterface $dateCourse): self
    {
        $this->dateCourse = $dateCourse;

        return $this;
    }
}
