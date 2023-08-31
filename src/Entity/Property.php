<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ApiResource]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:get', "habitat:get", "habitat:collection",
        "habitat:property:get", "habitat:property:collection", "habitat:property:post"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:get', "habitat:get", "habitat:collection",
        "habitat:property:get", "habitat:property:collection","habitat:property:post"])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['category:get', "habitat:get", "habitat:collection",
        "habitat:property:get", "habitat:property:collection","habitat:property:post"])]
    private ?bool $required = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    #[Groups(["habitat:get", "habitat:collection",
        "habitat:property:get", "habitat:property:collection","habitat:property:post"])]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: HabitatProperty::class)]
    private Collection $habitatProperties;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "create")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "update")]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->habitatProperties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

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
            $habitatProperty->setProperty($this);
        }

        return $this;
    }

    public function removeHabitatProperty(HabitatProperty $habitatProperty): self
    {
        if ($this->habitatProperties->removeElement($habitatProperty)) {
            // set the owning side to null (unless already changed)
            if ($habitatProperty->getProperty() === $this) {
                $habitatProperty->setProperty(null);
            }
        }

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
