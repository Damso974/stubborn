<?php

namespace App\Controller;

use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->getCart();

        return $this->render('cart/index.html.twig', [
            'items' => $cart['items'],
            'total' => $cart['total'],
        ]);
    }

    #[Route('/cart/remove/{key}', name: 'app_cart_remove')]
    public function remove(string $key, CartService $cartService): Response
    {
        $cartService->remove($key);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(
        CartService $cartService,
        StripeService $stripeService
    ): Response {
        $cart = $cartService->getCart();

        if (empty($cart['items'])) {
            $this->addFlash(
                'error',
                'Votre panier est vide.'
            );

            return $this->redirectToRoute('app_cart');
        }

        $successUrl = $this->generateUrl(
            'app_cart_success',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $cancelUrl = $this->generateUrl(
            'app_cart_cancel',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $checkoutSession = $stripeService->createCheckoutSession(
            $cart,
            $successUrl,
            $cancelUrl
        );

        return $this->redirect($checkoutSession->url, 303);
    }

    #[Route('/cart/success', name: 'app_cart_success')]
    public function success(
        Request $request,
        CartService $cartService,
        StripeService $stripeService
    ): Response {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            $this->addFlash(
                'error',
                'Impossible de vérifier le paiement.'
            );

            return $this->redirectToRoute('app_cart');
        }

        try {
            $checkoutSession = $stripeService->retrieveCheckoutSession(
                $sessionId
            );
        } catch (\Throwable $exception) {
            $this->addFlash(
                'error',
                'La session de paiement Stripe est invalide.'
            );

            return $this->redirectToRoute('app_cart');
        }

        if ($checkoutSession->payment_status !== 'paid') {
            $this->addFlash(
                'error',
                'Le paiement n’a pas été confirmé.'
            );

            return $this->redirectToRoute('app_cart');
        }

        // Le paiement est confirmé :
        // on diminue les stocks avant de vider le panier.
        $cartService->decreaseStock();

        $cartService->clear();

        return $this->render('cart/success.html.twig');
    }

    #[Route('/cart/cancel', name: 'app_cart_cancel')]
    public function cancel(): Response
    {
        return $this->render('cart/cancel.html.twig');
    }
}
