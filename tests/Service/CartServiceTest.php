<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartServiceTest extends TestCase
{
    private function createProduct(): Product
    {
        return (new Product())
            ->setName('Blackbelt')
            ->setPrice('29.90')
            ->setImage('blackbelt.jpg')
            ->setFeatured(true)
            ->setStockXs(2)
            ->setStockS(2)
            ->setStockM(2)
            ->setStockL(2)
            ->setStockXl(2);
    }

    private function createCartService(
        ProductRepository $productRepository,
        Session $session
    ): CartService {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new CartService(
            $requestStack,
            $productRepository,
            $entityManager
        );
    }

    public function testAddProductToCart(): void
    {
        $product = $this->createProduct();

        $productRepository = $this->createMock(ProductRepository::class);

        $productRepository
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $session = new Session(
            new MockArraySessionStorage()
        );

        $cartService = $this->createCartService(
            $productRepository,
            $session
        );

        $result = $cartService->add(1, 'M');

        $this->assertTrue($result);

        $cart = $session->get('cart');

        $this->assertArrayHasKey('1_M', $cart);

        $this->assertSame(
            1,
            $cart['1_M']['quantity']
        );

        $this->assertSame(
            'M',
            $cart['1_M']['size']
        );
    }

    public function testCannotAddMoreThanAvailableStock(): void
    {
        $product = $this->createProduct();

        $productRepository = $this->createMock(ProductRepository::class);

        $productRepository
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $session = new Session(
            new MockArraySessionStorage()
        );

        $cartService = $this->createCartService(
            $productRepository,
            $session
        );

        $firstAdd = $cartService->add(1, 'M');
        $secondAdd = $cartService->add(1, 'M');
        $thirdAdd = $cartService->add(1, 'M');

        $this->assertTrue($firstAdd);
        $this->assertTrue($secondAdd);
        $this->assertFalse($thirdAdd);

        $cart = $session->get('cart');

        $this->assertSame(
            2,
            $cart['1_M']['quantity']
        );
    }

    public function testCartTotalIsCalculatedCorrectly(): void
    {
        $product = $this->createProduct();

        $productRepository = $this->createMock(ProductRepository::class);

        $productRepository
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $session = new Session(
            new MockArraySessionStorage()
        );

        $cartService = $this->createCartService(
            $productRepository,
            $session
        );

        $cartService->add(1, 'M');
        $cartService->add(1, 'M');

        $cart = $cartService->getCart();

        $this->assertCount(
            1,
            $cart['items']
        );

        $this->assertSame(
            2,
            $cart['items'][0]['quantity']
        );

        $this->assertEquals(
            59.80,
            $cart['items'][0]['subtotal']
        );

        $this->assertEquals(
            59.80,
            $cart['total']
        );
    }
}
