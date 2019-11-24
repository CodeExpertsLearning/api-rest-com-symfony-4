<?php
/**
 * Created by PhpStorm.
 * User: NandoKstroNet
 * Date: 24/11/19
 * Time: 19:15
 */

namespace App\Api;


class FilterTransform
{
	static public function transformFilters(string $filters): array
	{
		$filters = explode(';', rtrim($filters, ';'));

		$filtersTransform = [];

		foreach($filters as $filter) {
			$filtersTransform[] = explode(':', $filter);
		}

		return $filtersTransform;
	}

	static public function transformFields($fields, $tableAlias)
	{
		$fields = explode(',', $fields);

		$fields = array_map(function($line) use($tableAlias){
			return $tableAlias . $line;
		}, $fields);

		$fields = implode(', ', $fields);

		return $fields;
	}
}