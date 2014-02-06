<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\ProductBundle\Controller\Api;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class ProductController
 *
 * @package Sonata\ProductBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductController
{
    /**
     * @var ProductManagerInterface
     */
    protected $productManager;

    /**
     * Constructor
     *
     * @param ProductManagerInterface $productManager
     */
    public function __construct(ProductManagerInterface $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * Returns a paginated list of products.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\Component\Product\ProductInterface", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for products list pagination (1-indexed)")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of products by page")
     * @QueryParam(name="orderBy", array=true, requirements="ASC|DESC", nullable=true, strict=true, description="Query products order by clause (key is field, value is direction")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/disabled products only?")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return ProductInterface[]
     */
    public function getProductsAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedFilters = array(
            'enabled' => "",
        );

        $page    = $paramFetcher->get('page') - 1;
        $count   = $paramFetcher->get('count');
        $orderBy = $paramFetcher->get('orderBy');
        $filters = array_intersect_key($paramFetcher->all(), $supportedFilters);

        foreach ($filters as $key => $value) {
            if (null === $value) {
                unset($filters[$key]);
            }
        }

        return $this->productManager->findBy($filters, $orderBy, $count, $page);
    }

    /**
     * Retrieves a specific product
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return ProductInterface
     */
    public function getProductAction($id)
    {
        return $this->getProduct($id);
    }

    /**
     * Retrieves a specific product's ProductCategories
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductCategoryInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return ProductCategoryInterface[]
     */
    public function getProductProductcategoriesAction($id)
    {
        return $this->getProduct($id)->getProductCategories();
    }

    /**
     * Retrieves a specific product's ProductCategories' categories
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\CategoryInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return CategoryInterface[]
     */
    public function getProductCategoriesAction($id)
    {
        return $this->getProduct($id)->getCategories();
    }

    /**
     * Retrieves a specific product's ProductCollections
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductCollectionInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return ProductCollectionInterface[]
     */
    public function getProductProductcollectionsAction($id)
    {
        return $this->getProduct($id)->getProductCollections();
    }

    /**
     * Retrieves a specific product's ProductCollections' collections
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\ClassificationBundle\Model\CollectionInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return CollectionInterface[]
     */
    public function getProductCollectionsAction($id)
    {
        return $this->getProduct($id)->getCollections();
    }

    /**
     * Retrieves a specific product's deliveries
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\DeliveryInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return DeliveryInterface[]
     */
    public function getProductDeliveriesAction($id)
    {
        return $this->getProduct($id)->getDeliveries();
    }

    /**
     * Retrieves a specific product's packages
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\PackageInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return PackageInterface[]
     */
    public function getProductPackagesAction($id)
    {
        return $this->getProduct($id)->getPackages();
    }

    /**
     * Retrieves a specific product's variations
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product id"}
     *  },
     *  output={"class"="Sonata\Component\Product\ProductInterface", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when product is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return ProductInterface[]
     */
    public function getProductVariationsAction($id)
    {
        return $this->getProduct($id)->getVariations();
    }

    /**
     * Retrieves product with id $id or throws an exception if it doesn't exist
     *
     * @param $id
     *
     * @return ProductInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getProduct($id)
    {
        $product = $this->productManager->findOneBy(array('id' => $id));

        if (null === $product) {
            throw new NotFoundHttpException(sprintf('Product (%d) not found', $id));
        }

        return $product;
    }

}