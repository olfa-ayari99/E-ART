<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SponsorRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    /**
     * @Assert\NotBlank
     */
    private ?string $nom_societe = null;
    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(length: 255)]
    private ?string $type_sponsor = null;
    /**
     * @Assert\NotBlank
     * @Assert\Regex("/^[0-9]+$/")
     */
    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column]
    /**
     * @Assert\NotBlank
     *  @Assert\PositiveOrZero
     */
    private ?float $montant = null;

    #[ORM\ManyToMany(targetEntity: Evenement::class, mappedBy: 'sponsor')]
    private Collection $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSociete(): ?string
    {
        return $this->nom_societe;
    }

    public function setNomSociete(string $nom_societe): self
    {
        $this->nom_societe = $nom_societe;

        return $this;
    }

    public function getTypeSponsor(): ?string
    {
        return $this->type_sponsor;
    }

    public function setTypeSponsor(string $type_sponsor): self
    {
        $this->type_sponsor = $type_sponsor;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->addSponsor($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeSponsor($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom_societe;
    }
}
