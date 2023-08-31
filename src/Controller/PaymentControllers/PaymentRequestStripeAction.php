<?php

namespace App\Controller\PaymentControllers;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

#[AsController]
class PaymentRequestStripeAction extends AbstractController
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function __invoke($id): array
    {
        Stripe::setApiKey($this->getParameter("app.stripe.secret"));

        $reservation = $this->reservationRepository->find($id);

        $paymentIntent = PaymentIntent::create([
            'amount' => $reservation->getHabitat()->getPrice(),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        return [$paymentIntent];
    }
}
