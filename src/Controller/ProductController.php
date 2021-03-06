<?php

namespace App\Controller;

use App\Api\FormErrorValidtation;
use App\Api\Message\ApiError;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Api\Service\PaginatorFactory;

/**
 * @Route("/products", name="products_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function index(Request $request, PaginatorFactory $paginator)
    {
    	$productRepository  = $this->getDoctrine()->getRepository(Product::class);

    	$fields = $request->query->get('fields', null);
	    $limit = $request->query->get('limit', null);
		$filters = $request->query->get('filters', null);

		$products = $productRepository->getProductsByFilters($filters, $fields, $limit);

		$products = $paginator->paginate($products, $request, 'products_index');

        return $this->json($products, 200, [], ['groups' => ['all']]);
    }

	/**
	 * @Route("/{productId}", name="show", methods={"GET"})
	 */
	public function show($productId)
	{
		$product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

		return $this->json([
			'data' => $product
		], 200, [], ['groups' => ['show']]);
	}

	/**
	 * @Route("/", name="create", methods={"POST"})
	 */
    public function create(Request $request, FormErrorValidtation $errorValidation)
    {
    	$productData = $request->request->all();

		$product = new Product();
//		$product->setName($productData['name']);
//		$product->setDescription($productData['description']);
//	    $product->setContent($productData['content']);
//	    $product->setPrice($productData['price']);
//		$product->setSlug($productData['slug']);

	    $form = $this->createForm(ProductType::class, $product);
	    $form->submit($productData);

	    if(!$form->isValid()) {
			$errors = new ApiError(
				'form_validation',
				'Validação dos campos do Formulário',
				$errorValidation->getErrors($form));

			return $this->json($errors, 400);
	    }

		$product->setIsActive(true);
		$doctrine = $this->getDoctrine()->getManager();

		$doctrine->persist($product);
		$doctrine->flush();

	    return $this->json([
		    'message' => 'Produto criado com sucesso!'
	    ]);
    }

	/**
	 * @Route("/{productId}", name="update", methods={"PUT", "PATCH"})
	 */
	public function update(Request $request, $productId, FormErrorValidtation $errorValidation)
	{
		$productData = $request->request->all();

		$doctrine = $this->getDoctrine();

		$product = $doctrine->getRepository(Product::class)->find($productId);

//		$product->setName($productData['name']);
//		$product->setDescription($productData['description']);
//		$product->setContent($productData['content']);
//		$product->setPrice($productData['price']);
//		$product->setSlug($productData['slug']);

		$form = $this->createForm(ProductType::class, $product);
		$form->submit($productData);

		if(!$form->isValid()) {
			$errors = new ApiError(
				'form_validation',
				'Validação dos campos do Formulário',
				$errorValidation->getErrors($form));

			return $this->json($errors, 400);
		}

		$manager = $doctrine->getManager();

		$manager->flush();

		return $this->json([
			'message' => 'Produto atualizado com sucesso!'
		]);
	}

	/**
	 * @Route("/{productId}", name="remove", methods={"DELETE"})
	 */
	public function remove($productId)
	{
		$doctrine = $this->getDoctrine();

		$product = $doctrine->getRepository(Product::class)->find($productId);

		$manager = $doctrine->getManager();
		$manager->remove($product);
		$manager->flush();

		return $this->json([
			'message' => 'Produto removido com sucesso!'
		]);
	}
}
