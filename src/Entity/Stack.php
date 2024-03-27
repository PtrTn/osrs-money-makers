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
    private ?Item $item = null;

    public function __toString(): string
    {
        return $this->item->getName() . ' (' . $this->quantity . ')';
    }

    public function getImage(): string
    {
        $file = $this->item->getGameId() . '.gif';
        if ($this->item->isMoney()) {
            if ($this->quantity > 1) {
                $file = 'coins_1.png';
            }
            if ($this->quantity > 25) {
                $file = 'coins_25.png';
            }
            if ($this->quantity > 100) {
                $file = 'coins_100.png';
            }
            if ($this->quantity > 1000) {
                $file = 'coins_1000.png';
            }
            if ($this->quantity > 10000) {
                $file = 'coins_10000.png';
            }
        }
        return 'images/items/' . $file;
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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): static
    {
        $this->item = $item;

        return $this;
    }
}
