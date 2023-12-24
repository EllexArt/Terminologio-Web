<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Concept::class)]
    private Collection $concepts;

    public function __construct()
    {
        $this->concepts = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Concept>
     */
    public function getConcepts(): Collection
    {
        return $this->concepts;
    }

    public function addConcept(Concept $concept): static
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->setCategory($this);
        }

        return $this;
    }

    public function removeConcept(Concept $concept): static
    {
        if ($this->concepts->removeElement($concept)) {
            // set the owning side to null (unless already changed)
            if ($concept->getCategory() === $this) {
                $concept->setCategory(null);
            }
        }

        return $this;
    }
}
