<?php

namespace App\Controller;


use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController
{

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }



    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello($name, Environment $twig)
    {
        $html = $twig->render('hello.html.twig', [
            'name' => $name,
            'ages' => [
                12,
                18,
                29,
                15
            ]
        ]);

        return new Response($html);
    }
}
