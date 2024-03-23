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

    #[ORM\Column]
    private ?int $ItemId = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Description = null;

    #[ORM\OneToOne(targetEntity: ItemPrice::class, inversedBy:'item')]
    #[ORM\JoinColumn(name:'item_id', referencedColumnName:'item_id', nullable: true, onDelete: 'SET NULL')]
    private ?ItemPrice $ItemPrice = null;

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
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getItemPrice(): ?ItemPrice
    {
        return $this->ItemPrice;
    }

    public function setItemPrice(?ItemPrice $ItemPrice): self
    {
        $this->ItemPrice = $ItemPrice;

        return $this;
    }
}
