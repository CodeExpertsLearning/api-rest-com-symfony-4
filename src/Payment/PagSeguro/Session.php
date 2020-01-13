<?php
namespace App\Payment\PagSeguro;


class Session extends PagSeguro
{
	public function createSession()
	{
		$sessionCode = \PagSeguro\Services\Session::create(
			\PagSeguro\Configuration\Configure::getAccountCredentials()
		);

		return $sessionCode->getResult();
	}
}