<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/product/new', name: 'app_admin_product_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le sweat-shirt a bien été ajouté.'
            );

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/product_new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'app_admin_product_edit')]
    public function edit(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le sweat-shirt a bien été modifié.'
            );

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/product_edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }
    #[Route('/admin/product/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
public function delete(
    Product $product,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    if ($this->isCsrfTokenValid(
        'delete-product-' . $product->getId(),
        $request->request->get('_token')
    )) {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le sweat-shirt a bien été supprimé.'
        );
    }

    return $this->redirectToRoute('app_admin');
}
}
