<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        var_dump('ça marche!');
        die;
    }
    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"},
     * schemes={"https", "http"})
     */
    public function test(Request $request, $age)
    {
        //$request = Request::createFromGlobals(); // car on a passé $request ds les parametres
        //dump($request);

        //$age = $request->query->get('age', 0);  
        //$age = $request->attributes->get('age', 0); // car on a mit $age ds les parametre de test

        // $age = 0;

        // if (!empty($_GET['age'])) {
        //     $age = $_GET['age'];
        // }
        //dd("Vous avez $age ans");

        return new Response("Vous avez $age ans");
    }
}
