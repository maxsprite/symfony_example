<?php

namespace App\Entity;

use App\Entity\Traits\MagicEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsultantRepository")
 */
class Consultant
{
    use MagicEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="consultant")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="consultants")
     */
    private $company;

    /**
     * @ORM\Column(type="string")
     */
    private $speciality;

    /**
     * @ORM\Column(type="integer")
     * @Groups("read")
     */
    private $price;

    /**
     * Min time for requests (in minutes)
     * @ORM\Column(type="integer")
     */
    private $minTime = 1;

    /**
     * @ORM\Column(type="float")
     */
    private $rating = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HashTag", mappedBy="consultant", cascade={"persist", "merge"}, orphanRemoval=true)
     */
    private $hashTags;

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
        $this->hashTags = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->price;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
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

    public function getMinTime(): ?int
    {
        return $this->minTime;
    }

    public function setMinTime(int $minTime): self
    {
        $this->minTime = $minTime;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection|HashTag[]
     */
    public function getHashTags(): Collection
    {
        return $this->hashTags;
    }

    public function addHashTag(HashTag $hashTag): self
    {
        if (!$this->hashTags->contains($hashTag)) {
            $this->hashTags[] = $hashTag;
            $hashTag->setConsultant($this);
        }

        return $this;
    }

    public function removeHashTag(HashTag $hashTag): self
    {
        if ($this->hashTags->contains($hashTag)) {
            $this->hashTags->removeElement($hashTag);
            // set the owning side to null (unless already changed)
            if ($hashTag->getConsultant() === $this) {
                $hashTag->setConsultant(null);
            }
        }

        return $this;
    }
}
