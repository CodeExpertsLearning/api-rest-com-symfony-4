<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/products", name="products_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
    	$products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->json([
            'data' => $products
        ], 200, [], ['groups' => ['all']]);
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
    public function create(Request $request)
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

		$product->setIsActive(true);
		$product->setCreatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));
		$product->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

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
	public function update(Request $request, $productId)
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

		$product->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

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
