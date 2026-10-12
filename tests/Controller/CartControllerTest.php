<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Service\CartService;
use App\Service\StripeService;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testCartPageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request('GET', '/cart');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'h1',
            'Mon panier'
        );
    }

    public function testCheckoutRedirectsWhenCartIsEmpty(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/cart/checkout'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testSuccessPageRequiresStripeSessionId(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/cart/success'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testCancelPageIsAccessible(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/cart/cancel'
        );

        $this->assertResponseIsSuccessful();
    }

    public function testCheckoutWithNonEmptyCartRedirectsToStripe(): void
    {
        $client = static::createClient();

        $product = (new Product())
            ->setName('Blackbelt')
            ->setPrice('29.90')
            ->setImage('blackbelt.jpg')
            ->setFeatured(true)
            ->setStockXs(2)
            ->setStockS(2)
            ->setStockM(2)
            ->setStockL(2)
            ->setStockXl(2);

        $cart = [
            'items' => [
                [
                    'key' => '1_M',
                    'product' => $product,
                    'size' => 'M',
                    'quantity' => 1,
                    'subtotal' => 29.90,
                ],
            ],
            'total' => 29.90,
        ];

        $cartService = $this->createMock(
            CartService::class
        );

        $cartService
            ->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $stripeSession = StripeCheckoutSession::constructFrom([
            'id' => 'cs_test_fake',
            'object' => 'checkout.session',
            'url' => 'https://checkout.stripe.com/test-session',
        ]);

        $stripeService = $this->createMock(
            StripeService::class
        );

        $stripeService
            ->expects($this->once())
            ->method('createCheckoutSession')
            ->willReturn($stripeSession);

        static::getContainer()->set(
            CartService::class,
            $cartService
        );

        static::getContainer()->set(
            StripeService::class,
            $stripeService
        );

        $client->request(
            'POST',
            '/cart/checkout'
        );

        $this->assertResponseRedirects(
            'https://checkout.stripe.com/test-session',
            303
        );
    }
}
