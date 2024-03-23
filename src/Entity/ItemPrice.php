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

    #[ORM\Column(nullable: true)]
    private ?int $avg = null;

    #[ORM\Column(nullable: true)]
    private ?int $min = null;

    #[ORM\Column(nullable: true)]
    private ?int $max = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvg(): ?int
    {
        return $this->avg;
    }

    public function setAvg(?int $avg): static
    {
        $this->avg = $avg;

        return $this;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): static
    {
        $this->max = $max;

        return $this;
    }
}
