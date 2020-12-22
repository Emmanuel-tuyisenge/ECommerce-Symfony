<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;

    protected $cartService;

    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {
        //lier avec le user connecté "security"
        $purchase->setUser($this->security->getUser());
        //->setPurchasedAt(new DateTime()) //car on l'a mit dans l'entity purchase 
        //->setTotal($this->cartService->getTotal()); // ajouté le calcul ds l'entity Purchase

        $this->em->persist($purchase);

        //lier avec les produits qui sont ds le panier =>"CartService"
        //$total = 0;

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            //$total += $cartItem->getTotal();

            $this->em->persist($purchaseItem);
        }

        //$purchase->setTotal($total);

        //enregistrer la commande =>"EntityManagerInterface"
        $this->em->flush();
    }
}
