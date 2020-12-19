<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**

     * @var Session
     */
    protected $session;

    /**

     * @var ProductRepository
     */
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }


    public function add(int $id)
    {
        $cart = $this->getCart();

        // if (array_key_exists($id, $cart)) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }

        //-----ce code est le même que celui d'haut---
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
        $cart[$id]++;

        //register updated table in our session 
        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}
