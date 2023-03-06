<?php

namespace App\Entity;

use App\Repository\GallerieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GallerieRepository::class)]
class Gallerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;


    #[Assert\NotBlank(message:"Ce champ est obligatoire")]
    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[Assert\NotBlank(message:"Ce champ est obligatoire")]
    #[ORM\Column(length: 255)]
    private ?string $adresse = null;


    #[Assert\NotBlank(message:"Ce champ est obligatoire")]
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;


    #[ORM\OneToMany(mappedBy: 'gallerie', targetEntity: Gallerie::class)]
    private Collection $galleries;


    #[ORM\ManyToOne(inversedBy: 'galleries', cascade: ["persist"])]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?UserGallerie $user = null;



    /**
     * @param int|null $id
     */
    public function __construct()
    {

    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?UserGallerie
    {
        return $this->user;
    }

    public function setUser(?UserGallerie $user): self
    {
        $this->user = $user;

        return $this;
    }
}
