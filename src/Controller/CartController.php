<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartFood;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function viewCart(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $user->getCart();

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart(int $id, FoodRepository $foodRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $food = $foodRepository->find($id);
        if (!$food) {
            throw $this->createNotFoundException('Food not found');
        }

        $cart = $user->getCart();
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $user->setCart($cart);
            $entityManager->persist($cart);
        }

        // Verificar si el CartFood ya existe
        $existingCartFood = $entityManager->getRepository(CartFood::class)->findOneBy(['cart' => $cart, 'food' => $food]);

        if ($existingCartFood) {
            // Si ya existe, incrementar la cantidad
            $existingCartFood->setQuantity($existingCartFood->getQuantity() + 1);
        } else {
            // Si no existe, crear una nueva instancia de CartFood
            $cartFood = new CartFood();
            $cartFood->setCart($cart);
            $cartFood->setFood($food);
            $cartFood->setQuantity(1);
            $entityManager->persist($cartFood);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Producto añadido al carrito');

        return $this->redirectToRoute('sym_menu');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(int $id, FoodRepository $foodRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $food = $foodRepository->find($id);
        if (!$food) {
            throw $this->createNotFoundException('Food not found');
        }

        $cart = $user->getCart();
        if (!$cart) {
            return $this->redirectToRoute('app_cart');
        }

        // Verificar si el CartFood ya existe
        $existingCartFood = $entityManager->getRepository(CartFood::class)->findOneBy(['cart' => $cart, 'food' => $food]);

        if ($existingCartFood) {
            // Si ya existe, decrementar la cantidad
            if ($existingCartFood->getQuantity() > 1) {
                $existingCartFood->setQuantity($existingCartFood->getQuantity() - 1);
            } else {
                // Si la cantidad es 1, eliminar el CartFood
                $entityManager->remove($existingCartFood);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_cart');
    }
}