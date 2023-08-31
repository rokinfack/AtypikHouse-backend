<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    private ProcessorInterface $decorated;
    private UserPasswordHasherInterface $_passwordHasher;

    public function __construct(ProcessorInterface $decorated, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->decorated = $decorated;
        $this->_passwordHasher = $userPasswordHasher;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data->getPlainPassword() != null) {
            $data->setPassword($this->_passwordHasher->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }
        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
