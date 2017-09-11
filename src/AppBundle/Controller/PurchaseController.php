<?php

namespace AppBundle\Controller;

use AppBundle\Form\PurchaseType;
use AppBundle\Entity\Purchase;
use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Purchase controller.
 *
 * @Route("purchase")
 */
class PurchaseController extends Controller
{
    /**
     * Lists all purchase entities.
     *
     * @Route("/", name="purchase_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $purchases = $em->getRepository(Purchase::class)->findByUser($this->getUser());

        return $this->render('purchase/index.html.twig', array(
            'purchases' => $purchases,
        ));
    }

    /**
     * Creates a new purchase entity.
     *
     * @Route("/new/{id}", name="purchase_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Product $product)
    {
        $purchase = new Purchase();
        $purchase->setPrice($product->getPrice());
        $purchase->setDiscount($product->getDiscount());
        $purchase->setProduct($product);
        $purchase->setUser($this->getUser());
        $purchase->setDate(new \DateTime());

        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // check if product price OR discount was changed before persisting Purchase
            if(!$purchase->isProductValid($product)) {
                $this->addFlash('error', "product data has been changed!");
                
                return $this->redirectToRoute('purchase_new', array('id' => $product->getId()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($purchase);
            $em->flush();

            return $this->redirectToRoute('purchase_show', array('id' => $purchase->getId()));
        }

        return $this->render('purchase/new.html.twig', array(
            'purchase' => $purchase,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a purchase entity.
     *
     * @Route("/{id}", name="purchase_show")
     * @Method("GET")
     */
    public function showAction(Purchase $purchase)
    {
        // check if purchase belongs to authenticated user
        if(!$purchase->isUserPurchase($this->getUser())) {
            return $this->redirectToRoute('purchase_index');
        }

        return $this->render('purchase/show.html.twig', array(
            'purchase' => $purchase,
        ));
    }
}
