<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;

    protected $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }


    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request)
    {
        //lire les donnéess du formulaire
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        //form pas rempli et soumis => dégager
        if (!$form->isSubmitted()) {
            //messager flash puis redirection 
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmer une commande');

            return $this->redirectToRoute('cart_show');
        }

        //pas connecté => dégage "securituy"
        $user = $this->getUser();

        //pas des produit dans le panier => dégager "cartService"
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez confirmer une commande avec une panier vide');

            return $this->redirectToRoute('cart_show');
        }

        //créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        //lier avec le user connecté "security"
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

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

        $this->cartService->empty();

        $this->addFlash('success', 'La commande a bien été enregistrée');

        return $this->redirectToRoute('purchase_index');
    }
}
