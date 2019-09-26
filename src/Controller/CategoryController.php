<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="categories_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
	    $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

	    return $this->json([
		    'data' => $categories
	    ], 200,[], ['groups' => ['all']]);
    }

	/**
	 * @Route("/{categoryId}", name="show", methods={"GET"})
	 */
	public function show($categoryId)
	{
		$category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);

		return $this->json([
			'data' => $category
		], 200,[], ['groups' => ['all', 'show_category']]);
	}

	/**
	 * @Route("/", name="create", methods={"POST"})
	 */
	public function create(Request $request)
	{
		$categoryData = $request->request->all();

		$category = new Category();

		$form = $this->createForm(CategoryType::class, $category);
		$form->submit($categoryData);

		$category->setCreatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));
		$category->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

		$doctrine = $this->getDoctrine()->getManager();
		$doctrine->persist($category);
		$doctrine->flush();

		return $this->json([
			'message' => 'Categoria criada com sucesso!'
		]);
	}

	/**
	 * @Route("/{categoryId}", name="update", methods={"PUT", "PATCH"})
	 */
	public function update(Request $request, $categoryId)
	{
		$categoryData = $request->request->all();

		$doctrine = $this->getDoctrine();

		$category = $doctrine->getRepository(Category::class)->find($categoryId);

		$form = $this->createForm(CategoryType::class, $category);
		$form->submit($categoryData);

		$category->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

		$manager = $doctrine->getManager();
		$manager->flush();

		return $this->json([
			'message' => 'Categoria atualizada com sucesso!'
		]);
	}

	/**
	 * @Route("/{categoryId}", name="remove", methods={"DELETE"})
	 */
	public function remove($categoryId)
	{
		$doctrine = $this->getDoctrine();

		$category = $doctrine->getRepository(Category::class)->find($categoryId);

		$manager = $doctrine->getManager();
		$manager->remove($category);
		$manager->flush();

		return $this->json([
			'message' => 'Categoria removida com sucesso!'
		]);
	}
}
