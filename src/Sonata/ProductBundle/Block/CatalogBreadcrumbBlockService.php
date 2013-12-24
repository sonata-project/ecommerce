<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\BaseBreadcrumbMenuBlockService;
use Symfony\Component\HttpFoundation\Response;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductInterface;

/**
 * BlockService for product catalog breadcrumb.
 *
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class CatalogBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
{
    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.product.block.breadcrumb';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu()
    {
        $parameters = array(
            'category' => $this->category,
            'product'  => $this->product,
        );

        return $this->menuBuilder->getBreadcrumbMenu($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $this->category = $blockContext->getBlock()->getSetting('category');
        $this->product  = $blockContext->getBlock()->getSetting('product');

        return parent::execute($blockContext, $response);
    }
}
