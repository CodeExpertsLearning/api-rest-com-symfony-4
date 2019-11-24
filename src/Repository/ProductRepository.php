<?php

namespace App\Repository;

use App\Api\FilterTransform;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function getProductsByFilters($filters = null, $fields = null, $limit = null)
    {
        $result =  $this->createQueryBuilder('p');

        if(!is_null($filters)) {
	        foreach(FilterTransform::transformFilters($filters) as $filter) {

		        $result->andWhere('p.' . $filter[0] . ' ' . $filter[1] . ' :' . $filter[0])
		               ->setParameter($filter[0], $filter[2]);
	        }
        }

        if(!is_null($fields)) {
            $result->select(FilterTransform::transformFields($fields, 'p.'));
        }

	    if(!is_null($limit)) {
		    $result->setMaxResults($limit);
	    }

	    return $result->getQuery()
            ->getResult()
        ;
    }
}
