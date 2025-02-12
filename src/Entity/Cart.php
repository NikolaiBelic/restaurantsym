<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'cart')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartFood::class, cascade: ['persist', 'remove'])]
    private Collection $cartFoods;

    public function __construct()
    {
        $this->cartFoods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CartFood>
     */
    public function getCartFoods(): Collection
    {
        return $this->cartFoods;
    }

    public function addCartFood(CartFood $cartFood): self
    {
        if (!$this->cartFoods->contains($cartFood)) {
            $this->cartFoods->add($cartFood);
            $cartFood->setCart($this);
        }

        return $this;
    }

    public function removeCartFood(CartFood $cartFood): self
    {
        if ($this->cartFoods->removeElement($cartFood)) {
            // set the owning side to null (unless already changed)
            if ($cartFood->getCart() === $this) {
                $cartFood->setCart(null);
            }
        }

        return $this;
    }
}
