<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KindRepository")
 */
class Kind
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $kindname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="kind")
     */
    private $transactions;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $iconColor;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKindname(): ?string
    {
        return $this->kindname;
    }

    public function setKindname(string $kindname): self
    {
        $this->kindname = $kindname;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setKind($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getKind() === $this) {
                $transaction->setKind(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->kindname;
    }

    public function getIconColor(): ?string
    {
        return $this->iconColor;
    }

    public function setIconColor(?string $iconColor): self
    {
        $this->iconColor = $iconColor;

        return $this;
    }
}
