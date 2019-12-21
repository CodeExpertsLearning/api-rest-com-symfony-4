<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Api\Upload;
use App\Entity\{ProductPhoto, Product};

/**
 * @Route("/products", name="product_")
 */
class ProductPhotoController extends AbstractController
{
	/**
	 * @Route("/{productId}/photos", name="photo_index", methods={"GET"})
	 */
	public function index($productId)
	{
		$product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

		return $this->json($product->getPhotos());
	}

    /**
     * @Route("/photos", name="photo", methods={"POST"})
     */
    public function create(Request $request, Upload $uploadService)
    {
    	try {
		    $uploadService->setAllowedFiles('jpg', 'png', 'jpeg');

		    $doctrine = $this->getDoctrine();
		    $product  = $doctrine->getRepository(Product::class)->find($request->get('product_id'));

		    $photos = $request->files->get('photos');
		    $photos = $uploadService->move($photos);

		    foreach($photos as $photo) {
			    $productPhoto = new ProductPhoto();
			    $productPhoto->setImage($photo);
			    $productPhoto->setCreatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));
			    $productPhoto->setUpdatedAt(new \DateTime("now", new \DateTimeZone('America/Sao_Paulo')));

			    $productPhoto->setProduct($product);

			    $doctrine->getManager()->persist($productPhoto);
			    $doctrine->getManager()->flush();
		    }

		    return $this->json([], 204);

	    } catch (\Exception $e) {
    		return $this->json([
    			'error' => [
    				'message' => $e->getMessage()
			    ]
		    ], 400);
	    }
    }

	/**
	 * @Route("/photos/{photoId}", name="photo_remove", methods={"DELETE"})
	 */
	public function remove(Request $request, $photoId)
	{
		$doctrine = $this->getDoctrine();
		$photo = $doctrine->getRepository(ProductPhoto::class)->find($photoId);

		if(file_exists($file = $this->getParameter('upload_dir') . '/' . $photo->getImage())) {
				unlink($file);
		}

		$doctrine->getManager()->remove($photo);
		$doctrine->getManager()->flush();

		return $this->json([], 204);
	}

}
