<?php

namespace App\Entity;

use App\Repository\ItemPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemPriceRepository::class)]
class ItemPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $ItemId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $examine = null;

    #[ORM\Column(nullable: true)]
    private ?int $avgPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $lowPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $highPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemId(): ?int
    {
        return $this->ItemId;
    }

    public function setItemId(int $ItemId): static
    {
        $this->ItemId = $ItemId;

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

    public function getAvgPrice(): ?int
    {
        return $this->avgPrice;
    }

    public function setAvgPrice(?int $avgPrice): static
    {
        $this->avgPrice = $avgPrice;

        return $this;
    }

    public function getLowPrice(): ?int
    {
        return $this->lowPrice;
    }

    public function setLowPrice(?int $lowPrice): static
    {
        $this->lowPrice = $lowPrice;

        return $this;
    }

    public function getHighPrice(): ?int
    {
        return $this->highPrice;
    }

    public function setHighPrice(?int $highPrice): static
    {
        $this->highPrice = $highPrice;

        return $this;
    }
}
