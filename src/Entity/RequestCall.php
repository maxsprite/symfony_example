<?php

namespace App\Entity;

use App\Entity\Traits\MagicEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestCallRepository")
 */
class RequestCall
{
    use MagicEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $consultant;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * In minutes
     *
     * @ORM\Column(type="integer")
     */
    private $time;

    /**
     * In minutes
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeCall;

    /**
     * Tax value in percent
     *
     * @ORM\Column(type="integer")
     */
    private $taxClientReject;

    /**
     * Tax value in percent
     *
     * @ORM\Column(type="integer")
     */
    private $taxConsultantSuccess;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestTransaction", mappedBy="requestCall")
     */
    private $requestTransactions;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->requestTransactions = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     * @return self
     */
    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTimeCall(): ?int
    {
        return $this->timeCall;
    }

    public function setTimeCall(int $timeCall): self
    {
        $this->timeCall = $timeCall;

        return $this;
    }

    public function getTaxClientReject(): ?int
    {
        return $this->taxClientReject;
    }

    public function setTaxClientReject(int $taxClientReject): self
    {
        $this->taxClientReject = $taxClientReject;

        return $this;
    }

    public function getTaxConsultantSuccess(): ?int
    {
        return $this->taxConsultantSuccess;
    }

    public function setTaxConsultantSuccess(int $taxConsultantSuccess): self
    {
        $this->taxConsultantSuccess = $taxConsultantSuccess;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getConsultant(): ?User
    {
        return $this->consultant;
    }

    public function setConsultant(?User $consultant): self
    {
        $this->consultant = $consultant;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|RequestTransaction[]
     */
    public function getRequestTransactions(): Collection
    {
        return $this->requestTransactions;
    }

    public function addRequestTransaction(RequestTransaction $requestTransaction): self
    {
        if (!$this->requestTransactions->contains($requestTransaction)) {
            $this->requestTransactions[] = $requestTransaction;
            $requestTransaction->setRequestCall($this);
        }

        return $this;
    }

    public function removeRequestTransaction(RequestTransaction $requestTransaction): self
    {
        if ($this->requestTransactions->contains($requestTransaction)) {
            $this->requestTransactions->removeElement($requestTransaction);
            // set the owning side to null (unless already changed)
            if ($requestTransaction->getRequestCall() === $this) {
                $requestTransaction->setRequestCall(null);
            }
        }

        return $this;
    }
}
