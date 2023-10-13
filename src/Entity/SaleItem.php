<?php

namespace App\Entity;

use App\Repository\SaleItemRepository;
use App\Repository\WineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleItemRepository::class)]
#[ORM\Table(name: 'sales_items')]
class SaleItem
{
    public ?Wine $wine = null;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Wine::class)]
    #[ORM\JoinColumn(name: 'wine_id', referencedColumnName: 'id', nullable: false)]
    private ?int $wine_id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $unit_price = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Sale::class)]
    #[ORM\JoinColumn(name: 'sale_id', referencedColumnName: 'id', nullable: false)]
    private ?int $sale_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWineId(): ?int
    {
        return $this->wine_id;
    }
    public function setWineId(int $wine_id): static
    {
        $this->wine_id = $wine_id;

        return $this;
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

    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    public function getSaleId(): ?int
    {
        return $this->sale_id;
    }

    public function setSaleId(int $sale_id): static
    {
        $this->sale_id = $sale_id;

        return $this;
    }
}
