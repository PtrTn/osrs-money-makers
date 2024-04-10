<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'prices')]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addPrice($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            $item->removePrice($this);
        }

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
