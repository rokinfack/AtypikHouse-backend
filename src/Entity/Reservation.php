<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\PaymentControllers\PaymentRequestStripeAction;
use App\Controller\PaymentControllers\PaymentRequestStripeController;
use App\Dto\Output\StandardResponseOutput;
use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(normalizationContext: ["groups" => ["reservation:get", "reservation:collection"]])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: "reservation/{id}/payment_request",
            status: 200,
            controller: PaymentRequestStripeAction::class,
            output: StandardResponseOutput::class,
            read: false,
            name: "reservation_request",
        ),
        new Put(uriTemplate: "reservation/{id}/payment")]
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["habitat:get", "habitat:collection"])]
    private ?\DateTimeInterface $releaseDay = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["habitat:get", "habitat:collection"])]
    private ?\DateTimeInterface $entryDay = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Habitat $habitat = null;

    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "create")]
    private ?\DateTimeImmutable $createdAt = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getReleaseDay(): ?\DateTimeInterface
    {
        return $this->releaseDay;
    }

    public function setReleaseDay(\DateTimeInterface $releaseDay): self
    {
        $this->releaseDay = $releaseDay;

        return $this;
    }

    public function getEntryDay(): ?\DateTimeInterface
    {
        return $this->entryDay;
    }

    public function setEntryDay(\DateTimeInterface $entryDay): self
    {
        $this->entryDay = $entryDay;

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): self
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setReservation($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getReservation() === $this) {
                $payment->setReservation(null);
            }
        }

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
}
