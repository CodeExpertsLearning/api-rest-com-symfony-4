<?php
namespace App\Payment\PagSeguro;


class CreditCard extends PagSeguro
{
	public function doPayment($data, $reference, $user)
	{
		$creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard();
		$creditCard->setReceiverEmail(\ConfigWrapper::EMAIL);
		$creditCard->setReference($reference);
		$creditCard->setCurrency("BRL");

		$creditCard->addItems()->withParameters(
			'0001',
			'Notebook prata',
			2,
			10.00
		);

		$email = \ConfigWrapper::ENV == 'sandbox' ? 'test@sandbox.pagseguro.uol.com.br' : $user->getEmail();
		$name  = $user->getFirstName() . ' ' . $user->getLastName();

		$creditCard->setSender()->setName($name);
		$creditCard->setSender()->setEmail($email);
		$creditCard->setSender()->setPhone()->withParameters(
			11,
			56273440
		);
		$creditCard->setSender()->setDocument()->withParameters(
			'CPF',
			'94739516802'
		);
		$creditCard->setSender()->setHash($data['hash']);
		$creditCard->setSender()->setIp('127.0.0.0');

		$creditCard->setShipping()->setAddress()->withParameters(
			'Av. Brig. Faria Lima',
			'1384',
			'Jardim Paulistano',
			'01452002',
			'São Paulo',
			'SP',
			'BRA',
			'apto. 114'
		);

		$creditCard->setBilling()->setAddress()->withParameters(
			'Av. Brig. Faria Lima',
			'1384',
			'Jardim Paulistano',
			'01452002',
			'São Paulo',
			'SP',
			'BRA',
			'apto. 114'
		);

		$creditCard->setToken($data['card_token']);

		list($installment, $amount) = explode('|', $data['installments']);

		$creditCard->setInstallment()->withParameters($installment, $amount);
		$creditCard->setHolder()->setBirthdate('01/10/1979');
		$creditCard->setHolder()->setName($data['card_name']);

		$creditCard->setHolder()->setPhone()->withParameters(
			11,
			56273440
		);
		$creditCard->setHolder()->setDocument()->withParameters(
			'CPF',
			'94739516802'
		);
		$creditCard->setMode('DEFAULT');

		$result = $creditCard->register(
			\PagSeguro\Configuration\Configure::getAccountCredentials()
		);

		return $result;
	}
}