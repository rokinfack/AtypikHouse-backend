<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordInput
{
    /**
     * @var string
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public string $token;

    /**
     * @var ?string
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[SerializedName("password_confirmation")]
    #[Assert\EqualTo(propertyPath: "password")]
    public ?string $plainPassword;

    /**
     * @var string
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public string $password;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

}