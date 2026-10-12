<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom du produit est obligatoire.'
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(
        message: 'Le prix est obligatoire.'
    )]
    #[Assert\Positive(
        message: 'Le prix doit être supérieur à 0.'
    )]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Le nom de l’image est obligatoire.'
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l’image ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $featured = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'Le stock XS est obligatoire.'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le stock XS ne peut pas être négatif.'
    )]
    private ?int $stockXs = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'Le stock S est obligatoire.'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le stock S ne peut pas être négatif.'
    )]
    private ?int $stockS = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'Le stock M est obligatoire.'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le stock M ne peut pas être négatif.'
    )]
    private ?int $stockM = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'Le stock L est obligatoire.'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le stock L ne peut pas être négatif.'
    )]
    private ?int $stockL = null;

    #[ORM\Column]
    #[Assert\NotNull(
        message: 'Le stock XL est obligatoire.'
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le stock XL ne peut pas être négatif.'
    )]
    private ?int $stockXl = null;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }

    public function getStockXs(): ?int
    {
        return $this->stockXs;
    }

    public function setStockXs(int $stockXs): static
    {
        $this->stockXs = $stockXs;

        return $this;
    }

    public function getStockS(): ?int
    {
        return $this->stockS;
    }

    public function setStockS(int $stockS): static
    {
        $this->stockS = $stockS;

        return $this;
    }

    public function getStockM(): ?int
    {
        return $this->stockM;
    }

    public function setStockM(int $stockM): static
    {
        $this->stockM = $stockM;

        return $this;
    }

    public function getStockL(): ?int
    {
        return $this->stockL;
    }

    public function setStockL(int $stockL): static
    {
        $this->stockL = $stockL;

        return $this;
    }

    public function getStockXl(): ?int
    {
        return $this->stockXl;
    }

    public function setStockXl(int $stockXl): static
    {
        $this->stockXl = $stockXl;

        return $this;
    }
}
