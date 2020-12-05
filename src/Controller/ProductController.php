<?php

namespace App\Controller;


use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }
    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository)
    {
        //dd($urlGenerator->generate('homepage'));

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,

        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit(
        $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {

        // $product = new Product;
        // $product->setName("Salut à tous")
        //     ->setPrice(200);

        // $resultat = $validator->validate($product);

        // if ($resultat->count() > 0) {
        //     dd("il y a des erreurs ", $resultat);
        // }


        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);

        // $form = $this->createForm(ProductType::class, $product, [
        //     "validation_groups" => ["large-name", "with-price"]
        // ]);
        //$form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $product = $form->getData(); //car product est ds le creatForm
            //dd($form->getData());
            $em->flush();

            // $response = new Response();
            // $url = $urlGenerator->generate('product_show', [
            //     'category_slug' => $product->getCategory()->getSlug(),
            //     'slug' => $product->getSlug()
            // ]);
            // $response->headers->set('Location', $url);
            // $response->setStatusCode(302); 
            //ce ligne en bas remplace les deux qui sont à haut
            // $response = new RedirectResponse($url);
            // return $response;

            //return $this->redirect($url); //grace au AbstrCtr on fait que cette derniere ligne

            //on remplace $url par son contenu qui est commenté ci dessus
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ) {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            // $product = new Product;
            // $product->setName($data['name'])
            //     ->setShortDescription($data['shortDescription'])
            //     ->setPrice($data['price'])
            //     ->setCategory($data['category']);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
