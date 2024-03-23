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

    #[ORM\Column(nullable: true)]
    private ?int $LatestPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $AvgPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $LowPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $HighPrice = null;

    #[ORM\OneToOne(targetEntity: Item::class)]
    private ?Item $item;

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

    public function getLatestPrice(): ?int
    {
        return $this->LatestPrice;
    }

    public function setLatestPrice(int $LatestPrice): static
    {
        $this->LatestPrice = $LatestPrice;

        return $this;
    }

    public function getAvgPrice(): ?int
    {
        return $this->AvgPrice;
    }

    public function setAvgPrice(int $AvgPrice): static
    {
        $this->AvgPrice = $AvgPrice;

        return $this;
    }

    public function getLowPrice(): ?int
    {
        return $this->LowPrice;
    }

    public function setLowPrice(int $LowPrice): static
    {
        $this->LowPrice = $LowPrice;

        return $this;
    }

    public function getHighPrice(): ?int
    {
        return $this->HighPrice;
    }

    public function setHighPrice(int $HighPrice): static
    {
        $this->HighPrice = $HighPrice;

        return $this;
    }
}
