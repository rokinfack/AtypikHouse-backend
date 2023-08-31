<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Filter\LocationDistanceFilter;
use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ["groups" => ["habitat:collection"]]),
    new Put(),
    new Patch(denormalizationContext: ["groups" => ["habitat:patch"]]),
    new Get(),
    new Post(),
    new Delete()
], normalizationContext: ["groups" => ["habitat:get"]], denormalizationContext: ["groups" => ["habitat:post"]])]
#[ApiFilter(DateFilter::class,
    strategy: DateFilterInterface::INCLUDE_NULL_BEFORE_AND_AFTER,
    properties: ['reservations.entryDay' => DateFilterInterface::PARAMETER_AFTER,
        'reservations.releaseDay' => DateFilterInterface::PARAMETER_BEFORE])]
#[ApiFilter(BooleanFilter::class, properties: ["isPublished"])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(LocationDistanceFilter::class, properties: ["latitude", "longitude", "distance"])]
#[ApiFilter(SearchFilter::class, properties: ["category" => SearchFilterInterface::STRATEGY_EXACT])]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch", "comment:get", "comment:collection"])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(["habitat:get", "habitat:collection"])]
    private ?int $notes = 0;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?string $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?string $latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?string $currency = null;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Report::class)]
    private Collection $reports;


    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: HabitatProperty::class)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private Collection $habitatProperties;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Reservation::class)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:patch"])]
    private Collection $reservations;

    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(unique: false, nullable: true, onDelete: "CASCADE")]
    #[ApiProperty(types: ['https://schema.org/image'])]
    #[Groups(["habitat:get", "habitat:collection", "habitat:patch"])]
    private ?Media $coverImage = null;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Media::class)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:patch"])]
    private Collection $imageHabitats;

    #[ORM\ManyToOne(inversedBy: 'habitats')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?string $location = null;


    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Comment::class)]
    #[Groups(["habitat:get", "habitat:collection"])]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'habitats')]
    #[Groups(["habitat:get", "habitat:collection", "habitat:post", "habitat:patch"])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'habitats')]
    private ?User $owner = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column]
    #[Groups(["habitat:get", "habitat:collection"])]
    #[Gedmo\Timestampable(on: "create")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "update")]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->habitatProperties = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->imageHabitats = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->setIsPublished(false);
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNotes(): ?int
    {
        return $this->notes;
    }

    public function setNotes(int $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $like->setHabitat($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getHabitat() === $this) {
                $like->setHabitat(null);
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
            $report->setHabitat($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getHabitat() === $this) {
                $report->setHabitat(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, HabitatProperty>
     */
    public function getHabitatProperties(): Collection
    {
        return $this->habitatProperties;
    }

    public function addHabitatProperty(HabitatProperty $habitatProperty): self
    {
        if (!$this->habitatProperties->contains($habitatProperty)) {
            $this->habitatProperties->add($habitatProperty);
            $habitatProperty->setHabitat($this);
        }

        return $this;
    }

    public function removeHabitatProperty(HabitatProperty $habitatProperty): self
    {
        if ($this->habitatProperties->removeElement($habitatProperty)) {
            // set the owning side to null (unless already changed)
            if ($habitatProperty->getHabitat() === $this) {
                $habitatProperty->setHabitat(null);
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
            $reservation->setHabitat($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getHabitat() === $this) {
                $reservation->setHabitat(null);
            }
        }

        return $this;
    }


    public function getCoverImage(): ?Media
    {
        return $this->coverImage;
    }

    public function setCoverImage(?Media $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getImageHabitats(): Collection
    {
        return $this->imageHabitats;
    }

    public function addImageHabitat(Media $imageHabitat): self
    {
        if (!$this->imageHabitats->contains($imageHabitat)) {
            $this->imageHabitats->add($imageHabitat);
            $imageHabitat->setHabitat($this);
        }

        return $this;
    }

    public function removeImageHabitat(Media $imageHabitat): self
    {
        if ($this->imageHabitats->removeElement($imageHabitat)) {
            // set the owning side to null (unless already changed)
            if ($imageHabitat->getHabitat() === $this) {
                $imageHabitat->setHabitat(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

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
            $comment->setHabitat($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getHabitat() === $this) {
                $comment->setHabitat(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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
}
