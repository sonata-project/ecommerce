<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller;

use Elastica\Query;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Currency\CurrencyDetector;
use Sonata\Component\Product\Pool;
use Sonata\DatagridBundle\Datagrid\Datagrid;
use Sonata\DatagridBundle\Pager\Elastica\Pager;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\Elastica\QueryBuilder;
use Sonata\ProductBundle\Entity\ProductSetManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SearchController
 */
class SearchController extends Controller
{
    /**
     * Process a search.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexAction(Request $request)
    {
        $displayMode = $request->query->get('mode', 'grid');

        if (!in_array($displayMode, array('grid'))) { // "list" mode will be added later
            throw new NotFoundHttpException(sprintf('Given display_mode "%s" doesn\'t exist.', $displayMode));
        }

        $builder = $this->get('sonata.product.search.provider.elastica');
        $builder->handleRequest($request);

        $datagrid = $builder->getDatagrid();

        $results = $datagrid->getResults();
        $provider = $results ? $this->getProductPool()->getProvider(current($results)) : null;

        return $this->render('SonataProductBundle:Search:index.html.twig', /**array_merge($builder->getSearchParameters(), */array(
            'category'     => null,
            'sort'         => null,
            'q'            => "",
            'currency'     => $this->getCurrencyDetector()->getCurrency(),
            'datagrid'     => $datagrid,
            'display_mode' => $displayMode,
            'form'         => $datagrid->getForm()->createView(),
            'provider'     => $provider,
        ))/*)*/;
    }

    /**
     * @return Pool
     */
    protected function getProductPool()
    {
        return $this->get('sonata.product.pool');
    }

    /**
     * @return ProductSetManager
     */
    protected function getProductSetManager()
    {
        return $this->get('sonata.product.set.manager');
    }

    /**
     * @return CurrencyDetector
     */
    protected function getCurrencyDetector()
    {
        return $this->get('sonata.price.currency.detector');
    }

    /**
     * Gets the product provider associated with $category if any
     *
     * @param CategoryInterface $category
     *
     * @return null|\Sonata\Component\Product\ProductProviderInterface
     */
    protected function getProviderFromCategory(CategoryInterface $category = null)
    {
        if (null === $category) {
            return null;
        }

        $product = $this->getProductSetManager()->findProductForCategory($category);

        return $product ? $this->getProductPool()->getProvider($product) : null;
    }
}
