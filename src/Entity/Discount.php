<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
#[UniqueEntity('codePrefix')]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 100)]
    private ?int $percentAmount = null;

    #[ORM\Column(name: 'code_prefix', length: 15, unique: true)]
    #[Assert\Length(min: 1, max: 15)]
    private ?string $codePrefix = null;

    /** @var Collection<int, DiscountCode> */
    #[ORM\OneToMany(targetEntity: DiscountCode::class, mappedBy: 'discount', orphanRemoval: true)]
    private Collection $discountCodes;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 1000000)]
    private ?int $numberOfCodes = 1;

    public function __construct()
    {
        $this->discountCodes = new ArrayCollection();
    }

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

    public function getPercentAmount(): ?int
    {
        return $this->percentAmount;
    }

    public function setPercentAmount(int $percentAmount): static
    {
        $this->percentAmount = $percentAmount;

        return $this;
    }

    public function getCodePrefix(): ?string
    {
        return $this->codePrefix;
    }

    public function setCodePrefix(string $codePrefix): static
    {
        $this->codePrefix = $codePrefix;

        return $this;
    }

    /** @return Collection<int, DiscountCode> */
    public function getDiscountCodes(): Collection
    {
        return $this->discountCodes;
    }

    public function addDiscountCode(DiscountCode $discountCode): static
    {
        if (!$this->discountCodes->contains($discountCode)) {
            $this->discountCodes->add($discountCode);
            $discountCode->setDiscount($this);
        }

        return $this;
    }

    public function removeDiscountCode(DiscountCode $discountCode): static
    {
        if ($this->discountCodes->removeElement($discountCode)) {
            // set the owning side to null (unless already changed)
            if ($discountCode->getDiscount() === $this) {
                $discountCode->setDiscount(null);
            }
        }

        return $this;
    }

    public function getNumberOfCodes(): ?int
    {
        return $this->numberOfCodes;
    }

    public function setNumberOfCodes(int $numberOfCodes): static
    {
        $this->numberOfCodes = $numberOfCodes;

        return $this;
    }
}
