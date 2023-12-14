<?php

namespace App\Entity;

use App\Repository\ComposantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComposantRepository::class)]
class Composant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'composants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Concept $concept = null;

    #[ORM\Column]
    private ?int $positionX = null;

    #[ORM\Column]
    private ?int $positionY = null;

    #[ORM\OneToMany(mappedBy: 'composant', targetEntity: ComposantName::class, orphanRemoval: true)]
    private Collection $composantNames;

    public function __construct()
    {
        $this->composantNames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getConcept(): ?Concept
    {
        return $this->concept;
    }

    public function setConcept(?Concept $concept): static
    {
        $this->concept = $concept;

        return $this;
    }

    public function getPositionX(): ?int
    {
        return $this->positionX;
    }

    public function setPositionX(int $positionX): static
    {
        $this->positionX = $positionX;

        return $this;
    }

    public function getPositionY(): ?int
    {
        return $this->positionY;
    }

    public function setPositionY(int $positionY): static
    {
        $this->positionY = $positionY;

        return $this;
    }

    /**
     * @return Collection<int, ComposantName>
     */
    public function getComposantNames(): Collection
    {
        return $this->composantNames;
    }

    public function addComposantName(ComposantName $composantName): static
    {
        if (!$this->composantNames->contains($composantName)) {
            $this->composantNames->add($composantName);
            $composantName->setComposant($this);
        }

        return $this;
    }

    public function removeComposantName(ComposantName $composantName): static
    {
        if ($this->composantNames->removeElement($composantName)) {
            // set the owning side to null (unless already changed)
            if ($composantName->getComposant() === $this) {
                $composantName->setComposant(null);
            }
        }

        return $this;
    }
}
