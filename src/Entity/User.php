<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\UserController;
use App\Repository\UserRepository;
use App\State\UserStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
//#[GetCollection(routeName: "google_start",
//    paginationEnabled: false,
//    output: false, read: false, write: false)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('USER_VIEW',object)"),
        new GetCollection(security: "is_granted('USER_VIEW_ALL',object)"),
        new Post(processor: UserStateProcessor::class),
        new Patch(denormalizationContext: ["groups" => ["user:patch"]], processor: UserStateProcessor::class),
        new Put(processor: UserStateProcessor::class),
        new Delete(),
    ],
    normalizationContext: ["groups" => ["user:get", "user:collection"]], denormalizationContext: ["groups" => ["user:write"]]
)]
#[UniqueEntity(['email'])]
#[UniqueEntity(fields: ["phoneNumber"], groups: ["user:phone"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    #[Assert\NotBlank(groups: ["user:write"])]
    #[Assert\Length(min: 6, max: 100)]
    #[Groups(["user:write", "user:get", "user:collection"])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(["user:get", "user:collection", "user:patch"])]
    #[ApiProperty(securityPostDenormalize: "is_granted('USER_EDIT', object)")]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    #[Groups(["user:write", "user:patch"])]
//    #[Assert\NotCompromisedPassword]
    #[Assert\NotNull(groups: ["user:write"])]
    private ?string $password = null;

    #[Groups(["user:write", "user:patch"])]
    #[SerializedName("password_confirmation")]
    #[Assert\NotNull(groups: ["user:write"])]
    #[Assert\NotBlank(groups: ["user:write"])]
    #[Assert\EqualTo(propertyPath: "password")]
    #[Assert\Length(min: 6, max: 100, groups: ["user:write"])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["user:write"])]
    #[Groups(["user:get", "user:write",
        "user:collection", "habitat:get", "habitat:collection", "comment:get", "comment:collection"])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ["user:write"])]
    #[Groups(["user:get", "user:write", "user:collection",
        "habitat:get", "habitat:collection",
        "comment:get", "comment:collection", "user:patch"])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Locale(groups: ["user:write"])]
    #[Groups(["user:get", "user:write", "user:collection", "habitat:get", "comment:get", "comment:collection"])]
    private ?string $locale = 'fr';

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["user:get", "user:write",
        "user:collection", "comment:get", "comment:collection"])]
    private ?\DateTimeInterface $birthDay = null;

    #[ORM\Column]
    #[Groups(["user:get", "user:collection", "user:patch"])]
    private ?bool $phoneNumberValidated = false;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "update")]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "create")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Report::class)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(unique: false, nullable: true)]
    #[ApiProperty(types: ['https://schema.org/image'])]
    #[Groups(["habitat:get", "user:get", "user:collection", "comment:get", "comment:collection"])]
    private ?Media $profileImage = null;

    #[ORM\OneToMany(mappedBy: 'senderUser', targetEntity: Notification::class)]
    private Collection $senderNotifications;

    #[ORM\OneToMany(mappedBy: 'receiverUser', targetEntity: Notification::class)]
    private Collection $receiverNotifications;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Habitat::class)]
    #[ApiProperty(security: "is_granted('ROLE_HOST') or object == user")]
    private Collection $habitats;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservation::class)]
    private Collection $reservations;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->senderNotifications = new ArrayCollection();
        $this->receiverNotifications = new ArrayCollection();
        $this->habitats = new ArrayCollection();
        $this->locale = 'fr';
        $this->updatedAt = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->reservations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function isPhoneNumberValidated(): ?bool
    {
        return $this->phoneNumberValidated;
    }

    public function setPhoneNumberValidated(bool $phoneNumberValidated): self
    {
        $this->phoneNumberValidated = $phoneNumberValidated;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

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
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    public function getProfileImage(): ?Media
    {

        return $this->profileImage;
    }

    public function setProfileImage(?Media $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getSenderNotifications(): Collection
    {
        return $this->senderNotifications;
    }

    public function addSenderNotification(Notification $senderNotification): self
    {
        if (!$this->senderNotifications->contains($senderNotification)) {
            $this->senderNotifications->add($senderNotification);
            $senderNotification->setSenderUser($this);
        }

        return $this;
    }

    public function removeSenderNotification(Notification $senderNotification): self
    {
        if ($this->senderNotifications->removeElement($senderNotification)) {
            // set the owning side to null (unless already changed)
            if ($senderNotification->getSenderUser() === $this) {
                $senderNotification->setSenderUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getReceiverNotifications(): Collection
    {
        return $this->receiverNotifications;
    }

    public function addReceiverNotification(Notification $receiverNotification): self
    {
        if (!$this->receiverNotifications->contains($receiverNotification)) {
            $this->receiverNotifications->add($receiverNotification);
            $receiverNotification->setReceiverUser($this);
        }

        return $this;
    }

    public function removeReceiverNotification(Notification $receiverNotification): self
    {
        if ($this->receiverNotifications->removeElement($receiverNotification)) {
            // set the owning side to null (unless already changed)
            if ($receiverNotification->getReceiverUser() === $this) {
                $receiverNotification->setReceiverUser(null);
            }
        }

        return $this;
    }


    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
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
     * @return Collection<int, Habitat>
     */
    public function getHabitats(): Collection
    {
        return $this->habitats;
    }

    public function addHabitat(Habitat $habitat): self
    {
        if (!$this->habitats->contains($habitat)) {
            $this->habitats->add($habitat);
            $habitat->setOwner($this);
        }

        return $this;
    }

    public function removeHabitat(Habitat $habitat): self
    {
        if ($this->habitats->removeElement($habitat)) {
            // set the owning side to null (unless already changed)
            if ($habitat->getOwner() === $this) {
                $habitat->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }


}
