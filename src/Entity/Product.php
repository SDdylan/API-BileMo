<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Hateoas\Relation("self", href = "expr('/api/products/' ~ object.getId())", exclusion = @Hateoas\Exclusion(groups={"product:list", "product:detail"}))
 *
 *
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"product:list", "product:detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"product:list", "product:detail"})
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"product:detail"})
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Groups({"product:detail"})
     */
    private $releaseDate;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"product:detail"})
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"product:detail", "product:list"})
     */
    private $priceHT;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"product:detail"})
     */
    private $stock;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"product:detail"})
     */
    private $isAvailable;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"product:detail", "product:list"})
     */
    private $brand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(float $priceHT): self
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

}
