<?php

namespace App\Messenger\Handler;

use App\Dto\ResetPasswordInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordHandler extends AbstractController implements MessageHandlerInterface
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private UserPasswordHasherInterface $_passwordHasher;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->_passwordHasher = $userPasswordHasher;
    }

    public function __invoke(ResetPasswordInput $resetPasswordInput): Response
    {
        $this->storeTokenInSession($resetPasswordInput->getToken());

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new Response(json_encode(["status" => "error", "message" => $e->getReason()]));
        }

        $this->resetPasswordHelper->removeResetRequest($token);

        if ($resetPasswordInput->getPlainPassword() != null) {
            $resetPasswordInput->setPassword($this->_passwordHasher->hashPassword($user, $resetPasswordInput->getPlainPassword()));
            $resetPasswordInput->eraseCredentials();
        }

        return new Response(json_encode([
            "status" => "success",
            "message" => "Votre mot de passe à été modifier avec success"
        ]));
    }
}