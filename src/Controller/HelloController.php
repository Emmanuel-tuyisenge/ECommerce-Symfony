<?php

namespace App\Controller;


use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    // protected $logger;

    // public function __construct(LoggerInterface $logger)
    // {
    //     $this->logger = $logger;
    // }

    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello(
        $name,
        LoggerInterface $logger,
        Calculator $calculator,
        Slugify $slugify
    ) {
        dump($slugify->slugify("Hello World!!"));

        $logger->error("Mon message de log !");
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $name");
    }
}
