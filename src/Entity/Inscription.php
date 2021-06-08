<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn as JoinColumn;
//use Doctrine\ORM\Mapping\JoinColumns;

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
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=circuit::class, inversedBy="inscriptions")
     * 
     */
    private $circuit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\Column(type="boolean")
     */
    private $listeAttente;

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

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getListeAttente(): ?bool
    {
        return $this->listeAttente;
    }

    public function setListeAttente(bool $listeAttente): self
    {
        $this->listeAttente = $listeAttente;

        return $this;
    }
}
