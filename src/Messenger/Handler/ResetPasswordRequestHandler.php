<?php

namespace App\Messenger\Handler;


use App\Dto\ResetPasswordRequestInput;
use App\Entity\ResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[AsMessageHandler]
class ResetPasswordRequestHandler extends AbstractController implements MessageHandlerInterface
{
    use ResetPasswordControllerTrait;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var ResetPasswordRequestRepository
     */
    private ResetPasswordRequestRepository $resetPasswordRequestRepository;
    /**
     * @var ResetPasswordHelperInterface
     */
    private ResetPasswordHelperInterface $resetPasswordHelper;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(UserRepository $userRepository, ResetPasswordRequestRepository $resetPasswordRequestRepository, ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer)
    {
        $this->userRepository = $userRepository;
        $this->resetPasswordRequestRepository = $resetPasswordRequestRepository;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function __invoke(ResetPasswordRequestInput $resetPasswordRequest): Response
    {
        $user = $this->userRepository->findOneByEmail($resetPasswordRequest->getEmail());

        if (!$user) {
            return new Response(json_encode(["status" => "error", "message" => "Utilisateur non trouvé"]), Response::HTTP_NOT_FOUND);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new Response(json_encode(["status" => "error", "message" => "Erreur: " . $e->getReason()]), Response::HTTP_NOT_FOUND);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@example.com', 'Password reset'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]);

        $this->mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return new Response(json_encode(["status" => "success"]), Response::HTTP_ACCEPTED);
    }
}