<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequestInput
{
    /**
     * @var string
     */
    #[Assert\NotNull]
    #[Assert\Email]
    public string $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

}