<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\Output\StandardResponseOutput;
use App\Dto\ResetPasswordInput;
use App\Dto\ResetPasswordRequestInput;
use App\Repository\ResetPasswordRequestRepository;
use App\State\StandardResponseStateProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
#[ApiResource(operations: [
    new Post(
        uriTemplate: "/reset_password/request.{_format}",
        status: 202,
        input: ResetPasswordRequestInput::class,
        output: StandardResponseOutput::class,
        messenger: true,
        provider: StandardResponseStateProvider::class
    )
])]
#[Post(
    uriTemplate: "/reset_password/",
    status: 201,
    input: ResetPasswordInput::class,
    output: StandardResponseOutput::class,
    messenger: true
)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): object
    {
        return $this->user;
    }
}
