<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Payment\PagSeguro\{Session, CreditCard};

/**
 * @Route("/checkout", name="checkout_")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("/session", name="session", methods={"GET"})
     */
    public function session(Session $pagseguroSession)
    {
        return $this->json([
           'data' => [
           	    'session_code' => $pagseguroSession->createSession()
           ]
        ]);
    }

    public function proccess(Request $request, CreditCard $pagseguroCreditCard)
    {

    }
}
