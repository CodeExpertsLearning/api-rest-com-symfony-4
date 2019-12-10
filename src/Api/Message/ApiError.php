<?php
namespace App\Api\Message;

class ApiError
{
	public $type;
	public $description;
	public $errors;

	public function __construct($type = null, $description = null, $errors = null)
	{
		$this->type = $type;
		$this->description = $description;
		$this->errors = $errors;
	}
}