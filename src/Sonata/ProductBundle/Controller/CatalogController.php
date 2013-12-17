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

use Sonata\Component\Currency\CurrencyDetector;
use Sonata\ProductBundle\Entity\ProductSetManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CatalogController extends Controller
{
    /**
     * Index action for catalog.
     */
    public function indexAction()
    {
        $maxPerPage = 9;

        $categoryId = $this->getRequest()->get('category');

        $pager = $this->getProductSetManager()
            ->getActiveProductsByCategoryIdPager($categoryId, $this->getRequest()->get('page'), $maxPerPage);

        return $this->render('SonataProductBundle:Catalog:index.html.twig', array(
            'display_mode' => $this->getRequest()->get('mode', 'grid'),
            'pager'        => $pager,
            'provider'     => $this->get('sonata.product.pool')->getProvider($pager->getCurrent()),
            'currency'     => $this->getCurrencyDetector()->getCurrency(),
        ));
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
}
