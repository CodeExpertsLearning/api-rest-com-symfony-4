<?php
namespace App\Listener;

use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
	/**
	 * @param ExceptionEvent $event
	 */
	public function onKernelException(ExceptionEvent $event)
	{
		$exception = $event->getException();
		$request   = $event->getRequest();

		if(in_array('application/json', $request->getAcceptableContentTypes())) {

			$statuCode = $exception instanceof HttpExceptionInterface
											   ? $exception->getStatusCode()
											   : Response::HTTP_INTERNAL_SERVER_ERROR;

			$event->setResponse(new JsonResponse(
				[
					'error' => [
						'code' => $statuCode,
						'message' => $exception->getMessage()
					]
				]
			));
		}
	}
}