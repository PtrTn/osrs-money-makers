<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    private const VOLUME_WARNING_PERCENTAGE = 0.01;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
            $input += $ingredient->getQuantity() * $ingredient->getItem()->getBuyPrice();
        }

        return $input * $this->throughput;
    }

    public function getOutput(): int
    {
        return $this->dish->getQuantity() * $this->dish->getItem()->getSellPrice() * $this->throughput;
    }

    public function getGrandExchangeCut(): int
    {
        return $this->getOutput() * 0.01;
    }

    public function getAvgProfit(): int
    {
        return $this->getOutput() - $this->getGrandExchangeCut() - $this->getInput();
    }

    public function getProfitColorClass(): string
    {
        $profit = $this->getAvgProfit();
        if ($profit > 500000) {
            return 'text-success';
        }
        if ($profit < 0) {
            return 'text-danger';
        }

        return '';
    }

    public function hasVolumeWarning(): bool
    {
        return count($this->getItemsWithVolumeWarning()) > 0;
    }

    /** @return Item[] */
    public function getItemsWithVolumeWarning(): array
    {
        $warnings = [];

        $requiredDishVolume = $this->dish->getQuantity() * $this->throughput;
        $dishVolume = $this->dish->getItem()->getPrice()->getSellVolume();
        if ($dishVolume !== null && $requiredDishVolume > $dishVolume * self::VOLUME_WARNING_PERCENTAGE) {
            $warnings[] = $this->dish->getItem();
        }
        foreach ($this->ingredients as $ingredient) {
            if ($ingredient->getItem()->isMoney()) {
                continue;
            }
            $requiredIngredientVolume = $ingredient->getQuantity() * $this->throughput;
            $ingredientVolume = $ingredient->getItem()->getPrice()->getBuyVolume();
            if ($ingredientVolume !== null && $requiredIngredientVolume > $ingredientVolume * self::VOLUME_WARNING_PERCENTAGE) {
                $warnings[] = $ingredient->getItem();
            }
        }

        return $warnings;
    }

    public function getId(): ?int
    {
        return $this->id;
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
