<?php

namespace Sonata\ProductBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\SeoBundle\Block\BreadcrumbBlockServiceInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BreadcrumbBlockService.
 *
 * @package Sonata\ProductBundle\Block
 * @author  Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class BreadcrumbBlockService extends BaseBlockService implements BreadcrumbBlockServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function handleContext($context)
    {
        return 'catalog' == $context;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $categories = null;
        $product    = null;

        if ($category = $blockContext->getBlock()->getSetting('category')) {
            $sorted = array($category);

            while ($c = $category->getParent()) {
                $sorted[] = $c;
                $category = $c;
            }

            $categories = array_reverse($sorted, true);
        }

        if ($product = $blockContext->getBlock()->getSetting('product')) {
            $category = $product->getMainCategory();

            $sorted = array($category);

            while ($c = $category->getParent()) {
                $sorted[] = $c;
                $category = $c;
            }

            $category = null;

            $categories = array_reverse($sorted, true);
        }

        return $this->renderResponse('SonataProductBundle:Catalog:breadcrumb.html.twig', array(
            'categories' => $categories,
            'product'    => $product,
        ), $response);
    }
}
