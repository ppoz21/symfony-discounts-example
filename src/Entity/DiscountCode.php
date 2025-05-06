<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DiscountCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: DiscountCodeRepository::class)]
#[UniqueEntity('code')]
class DiscountCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'code', length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'discountCodes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Discount $discount = null;

    #[ORM\Column]
    private ?bool $used = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }
}
