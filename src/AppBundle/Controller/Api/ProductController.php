<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Product;

class ProductController extends Controller
{

    /**
     * @Rest\Get("/api/product")
     */
    public function productsAction()
    {
        $result = $this->getDoctrine()->getRepository(Product::class)->findAll();

        if ($result === null) {
            return new View("there are no products", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @Rest\Get("/api/product/{id}")
     */
    public function productAction($id)
    {
        $result = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($result === null) {
            return new View("product not found", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}