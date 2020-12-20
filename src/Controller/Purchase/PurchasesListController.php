<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{

    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous dezes être connecté pour accéder à vos commandes !")
     */
    public function index()
    {
        //1 assure que la prs est connecté (service security)
        /** @var User */
        $user = $this->getUser();

        // if (!$user) { // ce if a été remplace par l'annotation ci_dessus @IsGranted
        //     // $url = $this->router->generate('homepage');
        //     // return new RedirectResponse($url);
        //     throw new AccessDeniedException("Vous dezes être connecté pour accéder à vos commandes !");
        // }

        //2 savoir qui est  connecé


        //3 passer le user connecté à twig
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
