<?php
namespace App\Api\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\RouterInterface;

class PaginatorFactory
{
	private $router;

	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}

	public function paginate(QueryBuilder $qb, Request $request, string $routeName)
	{
		$currentPage = $request->get('page', 1);

		$doctrineAdapter = new DoctrineORMAdapter($qb);
		$paginator       = new Pagerfanta($doctrineAdapter);
		$paginator->setMaxPerPage(2);
		$paginator->setCurrentPage($currentPage);

		$data = [];
		$items = $paginator->getCurrentPageResults();

		foreach ($items as $d) {
			$data[] = $d;
		}

		$paginationResult = [
			'data' => $data,
			'current_count' => count($items),
			'total' => $paginator->getNbResults()
		];

		$paginationResult['_links'] = PaginatorLinksFactory::mount($paginator, $routeName, $this->router, $request->query->all());

		return $paginationResult;
	}
}