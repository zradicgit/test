<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Purchase;

class PurchaseController extends Controller
{

    /**
     * @Rest\Get("/api/purchase")
     */
    public function purchasesAction()
    {
        $result = $this->getDoctrine()->getRepository(Purchase::class)->findByUser($this->getUser());

        if ($result === null) {
            return new View("there are no purchases", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @Rest\Get("/api/purchase/{id}")
     */
    public function purchaseAction($id)
    {
        $result = $this->getDoctrine()->getRepository(Purchase::class)->find($id);
        
        // check if purchase exists
        if ($result === null) {
            return new View("purchase not found", Response::HTTP_NOT_FOUND);
        }

        // check if purchase belongs to authenticated user
        if (!$result->isUserPurchase($this->getUser())) {
            return new View("purchase belongs to diferent user", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }
}