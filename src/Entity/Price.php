<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $high = null;

    #[ORM\Column(nullable: true)]
    private ?int $low = null;

    #[ORM\Column(nullable: true)]
    private ?int $minHigh = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxHigh = null;

    #[ORM\Column(nullable: true)]
    private ?int $minLow = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxLow = null;

    #[ORM\Column(nullable: true)]
    private ?int $highVolume = null;

    #[ORM\Column(nullable: true)]
    private ?int $lowVolume = null;

    public function getSellPrice(): ?int
    {
        return $this->getLow();
    }

    public function getBuyPrice(): ?int
    {
        return $this->getHigh();
    }

    public function getLowestBuyPrice(): ?int
    {
        return $this->getMinHigh();
    }

    public function getHighestBuyPrice(): ?int
    {
        return $this->getMaxHigh();
    }

    public function getLowestSellPrice(): ?int
    {
        return $this->getMinLow();
    }

    public function getHighestSellPrice(): ?int
    {
        return $this->getMaxLow();
    }

    public function getBuyVolume(): ?int
    {
        return $this->getHighVolume();
    }

    public function getSellVolume(): ?int
    {
        return $this->getLowVolume();
    }

    public function getSellPriceStatus(): string
    {
        if ($this->getHighestSellPrice() === null || $this->getLowestSellPrice() === null) {
            return '';
        }
        $range = $this->getHighestSellPrice() - $this->getLowestSellPrice();
        $goodThreshold = $this->getHighestSellPrice() - $range * 0.3;
        $badThreshold = $this->getLowestSellPrice() + $range * 0.3;

        if ($this->getLow() > $goodThreshold) {
            return 'text-success';
        }
        if ($this->getLow() < $badThreshold) {
            return 'text-danger';
        }

        return '';
    }

    public function getBuyPriceStatus(): string
    {
        if ($this->getHighestBuyPrice() === null || $this->getLowestBuyPrice() === null) {
            return '';
        }
        $range = $this->getHighestBuyPrice() - $this->getLowestBuyPrice();
        $badThreshold = $this->getHighestBuyPrice() - $range * 0.3;
        $goodThreshold = $this->getLowestBuyPrice() + $range * 0.3;

        if ($this->getHigh() < $goodThreshold) {
            return 'text-success';
        }
        if ($this->getHigh() > $badThreshold) {
            return 'text-danger';
        }

        return '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHigh(): ?int
    {
        return $this->high;
    }

    public function setHigh(?int $high): static
    {
        $this->high = $high;

        return $this;
    }

    public function getLow(): ?int
    {
        return $this->low;
    }

    public function setLow(?int $low): static
    {
        $this->low = $low;

        return $this;
    }

    public function getMinHigh(): ?int
    {
        return $this->minHigh;
    }

    public function setMinHigh(?int $minHigh): static
    {
        $this->minHigh = $minHigh;

        return $this;
    }

    public function getMaxHigh(): ?int
    {
        return $this->maxHigh;
    }

    public function setMaxHigh(?int $maxHigh): static
    {
        $this->maxHigh = $maxHigh;

        return $this;
    }

    public function getMinLow(): ?int
    {
        return $this->minLow;
    }

    public function setMinLow(?int $minLow): static
    {
        $this->minLow = $minLow;

        return $this;
    }

    public function getMaxLow(): ?int
    {
        return $this->maxLow;
    }

    public function setMaxLow(?int $maxLow): static
    {
        $this->maxLow = $maxLow;

        return $this;
    }

    public function getHighVolume(): ?int
    {
        return $this->highVolume;
    }

    public function setHighVolume(?int $highVolume): static
    {
        $this->highVolume = $highVolume;

        return $this;
    }

    public function getLowVolume(): ?int
    {
        return $this->lowVolume;
    }

    public function setLowVolume(?int $lowVolume): static
    {
        $this->lowVolume = $lowVolume;

        return $this;
    }
}
