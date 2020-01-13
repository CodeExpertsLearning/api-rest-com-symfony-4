<?php
namespace App\Payment\PagSeguro;


class PagSeguro
{
	public function __construct()
	{
		\PagSeguro\Library::initialize();
		\PagSeguro\Library::cmsVersion()->setName("API")->setRelease("1.0.0");
		\PagSeguro\Library::moduleVersion()->setName("API")->setRelease("1.0.0");
	}
}