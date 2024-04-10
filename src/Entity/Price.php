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
    private ?int $historicHigh = null;

    #[ORM\Column(nullable: true)]
    private ?int $historicLow = null;

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

    public function getHistoricHigh(): ?int
    {
        return $this->historicHigh;
    }

    public function setHistoricHigh(?int $historicHigh): static
    {
        $this->historicHigh = $historicHigh;

        return $this;
    }

    public function getHistoricLow(): ?int
    {
        return $this->historicLow;
    }

    public function setHistoricLow(?int $historicLow): static
    {
        $this->historicLow = $historicLow;

        return $this;
    }
}
