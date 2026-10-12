<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products')]
    public function index(
        Request $request,
        ProductRepository $productRepository
    ): Response {
        $priceRange = $request->query->get('price');

        $products = match ($priceRange) {
            '10-29' => $productRepository->findByPriceRange(10, 29),
            '29-35' => $productRepository->findByPriceRange(29, 35),
            '35-50' => $productRepository->findByPriceRange(35, 50),
            default => $productRepository->findAll(),
        };

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'priceRange' => $priceRange,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(
        int $id,
        Request $request,
        ProductRepository $productRepository,
        CartService $cartService
    ): Response {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        if ($request->isMethod('POST')) {
            $size = $request->request->get('size');

            $allowedSizes = ['XS', 'S', 'M', 'L', 'XL'];

            if (!in_array($size, $allowedSizes, true)) {
                $this->addFlash('error', 'Taille invalide.');

                return $this->redirectToRoute('app_product_show', [
                    'id' => $id,
                ]);
            }

            $added = $cartService->add($product->getId(), $size);

            if (!$added) {
                $this->addFlash(
                    'error',
                    'Stock insuffisant pour cette taille.'
                );

                return $this->redirectToRoute('app_product_show', [
                    'id' => $id,
                ]);
            }

            $this->addFlash(
                'success',
                'Le produit a bien été ajouté au panier.'
            );

            return $this->redirectToRoute('app_product_show', [
                'id' => $id,
            ]);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
