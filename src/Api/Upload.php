<?php
namespace App\Api;


class Upload
{
	private $directory;

	private $allowedFiles = [];

	public function __construct($directory)
	{
		$this->directory = $directory;
	}

	public function setAllowedFiles(...$files)
	{
		$this->allowedFiles = $files;
	}

	public function move(array $files)
	{
		$uploadedFiles = [];

		foreach($files as $file) {

			if(!in_array($file->guessExtension(), $this->allowedFiles)) {
				$allowedExtensions = implode(', ', $this->allowedFiles);
				throw new \Exception('Arquivo não é aceito neste upload. Extensões aceitas: ' . $allowedExtensions);
			}

			$imageName = $this->newFileName($file);

			$file->move($this->directory, $imageName);
			$uploadedFiles[] = $imageName;
		}

		return $uploadedFiles;
	}

	private function newFileName($file)
	{
		return sha1($file->getClientOriginalName()) . uniqid() . '.' . $file->guessExtension();
	}
}