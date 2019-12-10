<?php
namespace App\Api;

use Symfony\Component\Form\FormInterface;

class FormErrorValidtation
{
	public function getErrors(FormInterface $form)
	{
		$errors = [];

		foreach($form->getErrors() as $error) {
			$errors[] = $error->getMessage();
		}

		foreach($form->all() as $childForm) {
			if($childForm instanceof  FormInterface) {
				if($e = $this->getErrors($childForm)) {
					$errors[$childForm->getName()] = $e;
				}
			}
		}

		return $errors;
	}
}