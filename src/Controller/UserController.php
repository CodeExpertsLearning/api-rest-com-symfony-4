<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Api\Message\ApiError;
use App\Api\FormErrorValidtation;

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
		], 200, [], ['groups' => 'list']);
	}

	/**
	 * @Route("/{userId}", name="show", methods={"GET"})
	 */
	public function show($userId)
	{
		$user = $this->getDoctrine()->getRepository(User::class)->find($userId);

		return $this->json([
			'data' => $user
		], 200, [], ['groups' => 'single']);
	}

	/**
	 * @Route("/", name="create", methods={"POST"})
	 */
	public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder, FormErrorValidtation $errorValidation)
	{
		$userData = $request->request->all();

		$user = new User();

		$form = $this->createForm(UserType::class, $user);
		$form->submit($userData);

		if(!$form->isValid()) {
			$errors = new ApiError(
				'form_validation',
				'Validação dos campos do Formulário',
				$errorValidation->getErrors($form));

			return $this->json($errors, 400);
		}

		$password = $passwordEncoder->encodePassword($user, $userData['password']);
		$user->setPassword($password);
		$user->setRoles('ROLE_USER');

		$user->setIsActive(true);

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
	public function update(Request $request, $userId, UserPasswordEncoderInterface $passwordEncoder, FormErrorValidtation $errorValidation)
	{
		$rolesLoggedUser = $this->getUser()->getRoles();

		$userData = $request->request->all();

		$doctrine = $this->getDoctrine();

		$user = $doctrine->getRepository(User::class)->find($userId);

		$form = $this->createForm(UserType::class, $user);
		$form->submit($userData);

		if(!$form->isValid()) {
			$errors = new ApiError(
				'form_validation',
				'Validação dos campos do Formulário',
				$errorValidation->getErrors($form));

			return $this->json($errors, 400);
		}

		if($request->request->has('role') && in_array('ROLE_ADMIN', $rolesLoggedUser)){
			$user->setRoles($request->request->get('role'));
		}

		if($request->request->has('password')) {
			$password = $passwordEncoder->encodePassword($user, $userData['password']);
			$user->setPassword($password);
		}

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
