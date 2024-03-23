<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $examine = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name:'price_id', referencedColumnName:'id')]
    private ?ItemPrice $price = null;

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

    public function getExamine(): ?string
    {
        return $this->examine;
    }

    public function setExamine(?string $examine): static
    {
        $this->examine = $examine;

        return $this;
    }

    public function getPrice(): ?ItemPrice
    {
        return $this->price;
    }

    public function setPrice(?ItemPrice $price): static
    {
        $this->price = $price;

        return $this;
    }
}
