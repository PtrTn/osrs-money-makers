<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    private const COINS_GAME_ID = 617;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $gameId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $examine = null;

    #[ORM\ManyToMany(targetEntity: Price::class, inversedBy: 'item')]
    #[ORM\OrderBy(['date' => 'DESC'])]
    private Collection $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function getSellPrice(): int
    {
        /** @var Price $mostRecentPrice */
        $mostRecentPrice = $this->prices->first();

        return $mostRecentPrice->getLow();
    }

    public function getBuyPrice(): int
    {
        /** @var Price $mostRecentPrice */
        $mostRecentPrice = $this->prices->first();

        return $mostRecentPrice->getHigh();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function isMoney(): bool
    {
        return $this->gameId === self::COINS_GAME_ID;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): ?int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
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

    public function getExamine(): ?string
    {
        return $this->examine;
    }

    public function setExamine(?string $examine): static
    {
        $this->examine = $examine;

        return $this;
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
        }

        return $this;
    }

    public function removePrice(Price $price): static
    {
        $this->prices->removeElement($price);

        return $this;
    }
}
