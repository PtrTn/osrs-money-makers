<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $throughput = null;

    #[ORM\ManyToMany(targetEntity: Stack::class, cascade: ['persist'])]
    /** @var $ingredients Collection<Stack> */
    private Collection $ingredients;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stack $dish = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

    public function getInput(): int
    {
        $input = 0;
        foreach ($this->ingredients as $ingredient) {
            $input += $ingredient->getQuantity() * $ingredient->getItem()->getAvgPrice();
        }

        return $input * $this->throughput;
    }

    public function getOutput(): int
    {
        return $this->dish->getQuantity() * $this->dish->getItem()->getAvgPrice() * $this->throughput;
    }

    public function getAvgProfit(): int
    {
        return $this->getOutput() - $this->getInput();
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

    public function getThroughput(): ?int
    {
        return $this->throughput;
    }

    public function setThroughput(int $throughput): static
    {
        $this->throughput = $throughput;

        return $this;
    }

    /**
     * @return Collection<int, Stack>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Stack $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Stack $ingredient): static
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

    public function getDish(): ?Stack
    {
        return $this->dish;
    }

    public function setDish(?Stack $dish): static
    {
        $this->dish = $dish;

        return $this;
    }
}
