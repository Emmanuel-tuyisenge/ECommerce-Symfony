<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success(
        $id,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $em,
        CartService $cartService
    ) {
        // récupère la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }

        //passer le au status payée
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        //vide le panier
        $cartService->empty();

        //redirection avec un flash
        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute("purchase_index");
    }
}
