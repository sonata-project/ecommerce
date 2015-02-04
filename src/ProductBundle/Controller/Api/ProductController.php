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

use JMS\Serializer\SerializationContext;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;


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
     * @var Pool
     */
    protected $productPool;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormatterPool
     */
    protected $formatterPool;

    /**
     * Constructor
     *
     * @param ProductManagerInterface $productManager Sonata product manager
     * @param Pool                    $productPool    Sonata product pool
     * @param FormFactoryInterface    $formFactory    Symfony form factory
     * @param FormatterPool           $formatterPool
     */
    public function __construct(ProductManagerInterface $productManager, Pool $productPool, FormFactoryInterface $formFactory, FormatterPool $formatterPool)
    {
        $this->productManager   = $productManager;
        $this->productPool      = $productPool;
        $this->formFactory      = $formFactory;
        $this->formatterPool    = $formatterPool;
    }

    /**
     * Returns a paginated list of products.
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\DatagridBundle\Pager\PagerInterface", "groups"="sonata_api_read"}
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
     * @return Sonata\DatagridBundle\Pager\PagerInterface
     */
    public function getProductsAction(ParamFetcherInterface $paramFetcher)
    {
        $supportedCriteria = array(
            'enabled' => '',
        );

        $page     = $paramFetcher->get('page');
        $limit    = $paramFetcher->get('count');
        $sort     = $paramFetcher->get('orderBy');
        $criteria = array_intersect_key($paramFetcher->all(), $supportedCriteria);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (!$sort) {
            $sort = array();
        } elseif (!is_array($sort)) {
            $sort = array($sort => 'asc');
        }

        return $this->productManager->getPager($criteria, $page, $limit, $sort);
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
     * Adds a product depending on the product provider
     *
     * @Post("/{provider}/products")
     *
     * @ApiDoc(
     *  resource=true,
     *  input={"class"="sonata_product_api_form_product", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ProductBundle\Entity\BaseProduct", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while product creation",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @Route(requirements={"provider"="[A-Za-z0-9._]"})
     *
     * @param string  $provider A product provider name
     * @param Request $request  A Symfony request
     *
     * @return Product
     *
     * @throws NotFoundHttpException
     */
    public function postProductAction($provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request);
    }

    /**
     * Updates a product
     *
     * @Put("/{provider}/products/{id}")

     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product identifier"},
     *      {"name"="provider", "dataType"="string", "requirement"="[A-Za-z0-9.]*", "description"="product provider"}
     *  },
     *  input={"class"="sonata_product_api_form_product", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\ProductBundle\Entity\BaseProduct", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while product update",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @Route(requirements={"provider"="[A-Za-z0-9.]*"})
     *
     * @param integer $id       A Product identifier
     * @param string  $provider A product provider name
     * @param Request $request  A Symfony request
     *
     * @return Product
     *
     * @throws NotFoundHttpException
     */
    public function putProductAction($id, $provider, Request $request)
    {
        return $this->handleWriteProduct($provider, $request, $id);
    }

    /**
     * Write a product, this method is used by both POST and PUT action methods
     *
     * @param string       $provider A product provider name
     * @param Request      $request  Symfony request
     * @param integer|null $id       A product identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWriteProduct($provider, $request, $id = null)
    {
        $product = $id ? $this->getProduct($id) : null;

        $manager = $this->productPool->getManager($provider);

        $form = $this->formFactory->createNamed(null, 'sonata_product_api_form_product', $product, array(
            'csrf_protection' => false,
            'data_class'      => $manager->getClass(),
            'provider_name'   => $provider,
        ));

        $form->bind($request);

        if ($form->isValid()) {
            $product = $form->getData();
            $product->setDescription($this->formatterPool->transform($product->getDescriptionFormatter(), $product->getRawDescription()));
            $product->setShortDescription($this->formatterPool->transform($product->getShortDescriptionFormatter(), $product->getRawShortDescription()));
            $manager->save($product);

            $view = \FOS\RestBundle\View\View::create($product);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }

    /**
     * Deletes a product
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="product identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when post is successfully deleted",
     *      400="Returned when an error has occurred while product deletion",
     *      404="Returned when unable to find product"
     *  }
     * )
     *
     * @param integer $id A Product identifier
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws NotFoundHttpException
     */
    public function deleteProductAction($id)
    {
        $product = $this->getProduct($id);
        $manager = $this->productPool->getManager($product);

        try {
            $manager->delete($product);
        } catch (\Exception $e) {
            return \FOS\RestBundle\View\View::create(array('error' => $e->getMessage()), 400);
        }

        return array('deleted' => true);
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
