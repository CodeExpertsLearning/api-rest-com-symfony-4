<?php
/**
 * Created by PhpStorm.
 * User: NandoKstroNet
 * Date: 01/12/19
 * Time: 11:03
 */

namespace App\Api\Service;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\RouterInterface;

class PaginatorLinksFactory
{
	static public function mount(Pagerfanta $pagerFanta, $routeName, RouterInterface $router, $routeParams = [])
	{
		$links = [
			'self'  => self::gernerateLink($routeName, $routeParams, $pagerFanta->getCurrentPage(), $router),
			'first' => self::gernerateLink($routeName, $routeParams, 1, $router),
			'last'  => self::gernerateLink($routeName, $routeParams, $pagerFanta->getNbPages(), $router),
		];

		if($pagerFanta->hasPreviousPage()) {
			$links['prev'] = self::gernerateLink($routeName, $routeParams, $pagerFanta->getPreviousPage(), $router);
		}

		if($pagerFanta->hasNextPage()) {
			$links['next'] = self::gernerateLink($routeName, $routeParams, $pagerFanta->getNextPage(), $router);
		}

		return $links;
	}

	static private function gernerateLink(string $routeName, array $routeParams = [], $page = 1, RouterInterface $router)
	{
		$fullLink = $router->generate($routeName, array_merge($routeParams, ['page' => $page]), RouterInterface::ABSOLUTE_URL);

		return $fullLink;
	}
}