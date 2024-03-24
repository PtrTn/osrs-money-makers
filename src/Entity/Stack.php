<?php

namespace App\Entity;

use App\Repository\StackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StackRepository::class)]
class Stack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemPrice $itemPrice = null;

    public function __toString(): string
    {
        return $this->itemPrice->getName() . ' (' . $this->quantity . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getItemPrice(): ?ItemPrice
    {
        return $this->itemPrice;
    }

    public function setItemPrice(ItemPrice $itemPrice): static
    {
        $this->itemPrice = $itemPrice;

        return $this;
    }
}
