<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="users_")
 */
class UserController extends AbstractController
{
	/**
	 * @Route("/", name="index", methods={"GET"})
	 */
	public function index()
	{
		$users = $this->getDoctrine()->getRepository(User::class)->findAll();

		return $this->json([
			'data' => $users
		]);
	}

	/**
	 * @Route("/{userId}", name="show", methods={"GET"})
	 */
	public function show($userId)
	{
		$user = $this->getDoctrine()->getRepository(User::class)->find($userId);

		return $this->json([
			'data' => $user
		]);
	}

	/**
	 * @Route("/", name="create", methods={"POST"})
	 */
	public function create(Request $request)
	{
		$userData = $request->request->all();

		$user = new User();

		$form = $this->createForm(UserType::class, $user);
		$form->submit($userData);

		$user->setIsActive(true);
		$user->setCreatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));
		$user->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

		$doctrine = $this->getDoctrine()->getManager();
		$doctrine->persist($user);
		$doctrine->flush();

		return $this->json([
			'message' => 'Usuário criado com sucesso!'
		]);
	}

	/**
	 * @Route("/{userId}", name="update", methods={"PUT", "PATCH"})
	 */
	public function update(Request $request, $userId)
	{
		$userData = $request->request->all();

		$doctrine = $this->getDoctrine();

		$user = $doctrine->getRepository(User::class)->find($userId);

		$form = $this->createForm(UserType::class, $user);
		$form->submit($userData);

		$user->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

		$manager = $doctrine->getManager();
		$manager->flush();

		return $this->json([
			'message' => 'Usuário atualizado com sucesso!'
		]);
	}

	/**
	 * @Route("/{userId}", name="remove", methods={"DELETE"})
	 */
	public function remove($userId)
	{
		$doctrine = $this->getDoctrine();

		$user = $doctrine->getRepository(User::class)->find($userId);

		$manager = $doctrine->getManager();
		$manager->remove($user);
		$manager->flush();

		return $this->json([
			'message' => 'Usuário removido com sucesso!'
		]);
	}
}
