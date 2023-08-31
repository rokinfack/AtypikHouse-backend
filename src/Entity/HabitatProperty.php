<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\HabitatPropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: HabitatPropertyRepository::class)]
#[ApiResource(
    normalizationContext: ["groups" => ["habitat:property:get", "habitat:property:collection"]],
    denormalizationContext: ["groups" => ["habitat:property:post"]])]
class HabitatProperty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["habitat:get", "habitat:collection", "habitat:property:get",
        "habitat:property:collection","habitat:property:post"])]
    private array $value = [];

    #[ORM\ManyToOne(inversedBy: 'habitatProperties')]
    #[Groups(["habitat:get", "habitat:collection", "habitat:property:get",
        "habitat:property:collection","habitat:property:post"])]
    private ?Property $property = null;

    #[ORM\ManyToOne(inversedBy: 'habitatProperties')]
    #[Groups(["habitat:property:get", "habitat:property:collection","habitat:property:post"])]
    private ?Habitat $habitat = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "create")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: "update")]
    private ?\DateTimeImmutable $updateAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

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

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}
