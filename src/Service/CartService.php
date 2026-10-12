<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function add(int $productId, string $size): bool
    {
        $session = $this->requestStack->getSession();

        $cart = $session->get(self::CART_KEY, []);

        $product = $this->productRepository->find($productId);

        if (!$product) {
            return false;
        }

        $stock = match ($size) {
            'XS' => $product->getStockXs(),
            'S' => $product->getStockS(),
            'M' => $product->getStockM(),
            'L' => $product->getStockL(),
            'XL' => $product->getStockXl(),
            default => 0,
        };

        $key = $productId . '_' . $size;

        $currentQuantity = $cart[$key]['quantity'] ?? 0;

        if ($currentQuantity >= $stock) {
            return false;
        }

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'productId' => $productId,
                'size' => $size,
                'quantity' => 1,
            ];
        }

        $session->set(self::CART_KEY, $cart);

        return true;
    }

    public function remove(string $key): void
    {
        $session = $this->requestStack->getSession();

        $cart = $session->get(self::CART_KEY, []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        $session->set(self::CART_KEY, $cart);
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();

        $cart = $session->get(self::CART_KEY, []);

        $items = [];
        $total = 0;

        foreach ($cart as $key => $cartItem) {
            $product = $this->productRepository->find($cartItem['productId']);

            if (!$product) {
                continue;
            }

            $subtotal = (float) $product->getPrice() * $cartItem['quantity'];

            $items[] = [
                'key' => $key,
                'product' => $product,
                'size' => $cartItem['size'],
                'quantity' => $cartItem['quantity'],
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function decreaseStock(): void
    {
        $cart = $this->getCart();

        foreach ($cart['items'] as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            switch ($item['size']) {
                case 'XS':
                    $product->setStockXs(
                        $product->getStockXs() - $quantity
                    );
                    break;

                case 'S':
                    $product->setStockS(
                        $product->getStockS() - $quantity
                    );
                    break;

                case 'M':
                    $product->setStockM(
                        $product->getStockM() - $quantity
                    );
                    break;

                case 'L':
                    $product->setStockL(
                        $product->getStockL() - $quantity
                    );
                    break;

                case 'XL':
                    $product->setStockXl(
                        $product->getStockXl() - $quantity
                    );
                    break;
            }
        }

        $this->entityManager->flush();
    }

    public function clear(): void
    {
        $session = $this->requestStack->getSession();

        $session->remove(self::CART_KEY);
    }
}
