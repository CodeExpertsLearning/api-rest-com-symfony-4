<?php

namespace App\Controller;

use App\Api\Message\ApiError;
use App\Repository\UserOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Payment\PagSeguro\{
	Notification, Session, CreditCard
};
use App\Entity\UserOrder;

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

	/**
	 * @Route("/proccess", name="proccess", methods={"POST"})
	 */
    public function proccess(Request $request, CreditCard $pagseguroCreditCard)
    {
		try {
			$user = $this->getUser();
			$data = $request->request->all();
			$reference = sha1($user->getId() . $user->getEmail()) . '_' . uniqid() . '_SF_STORE';

			$proccess = $pagseguroCreditCard->doPayment($data, $reference, $user);

			$userOrder = new UserOrder();
			$userOrder->setReference($reference);
			$userOrder->setItems(serialize($data['products']));
			$userOrder->setPagseguroCode($proccess->getCode());
			$userOrder->setPagseguroStatus($proccess->getStatus());
			$userOrder->setCreatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")));
			$userOrder->setUpdatedAt(new \DateTime("now", new \DateTimeZone("America/Sao_Paulo")));

			$userOrder->setUser($user);

			$doctrine = $this->getDoctrine();
			$doctrine->getManager()->persist($userOrder);
			$doctrine->getManager()->flush();

			return $this->json([], 204);

		} catch (\Exception $e) {
			$error = new ApiError('checkout', $e->getMessage(), ['code' => 233003]);

			return $this->json($error, 400);
		}
    }

	/**
	 * @Route("/notification", name="notification", methods={"POST"})
	 */
	public function notification(Request $request, Notification $notification, UserOrderRepository $userOrderRep)
	{
		try {

			$notification = $notification->getTransaction();
			$userOrderRep = $userOrderRep->findOneByReference($notification->getReference());

			$userOrderRep->setPagseguroStatus($notification->getStatus());

			$manager = $this->getDoctrine()->getManager();
			$manager->flush();

			if($notification->getStatus() == 3) {
				//TO-DO: liberar o pedido do usuário, separar os items do pedido para envio...
			}


			return $this->json([], 204);

		} catch (\Exception $e) {
			$error = new ApiError('checkout', $e->getMessage(), ['code' => 233003]);

			return $this->json($error, 400);
		}
	}

}