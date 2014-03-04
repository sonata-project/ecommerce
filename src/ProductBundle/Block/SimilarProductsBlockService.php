<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Block;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Product\ProductFinderInterface;
use Sonata\ProductBundle\Repository\BaseProductRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class SimilarProductsBlockService extends BaseBlockService
{
    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * @var ProductFinderInterface
     */
    protected $productFinder;

    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @param string                    $name
     * @param EngineInterface           $templating
     * @param RegistryInterface         $registry
     * @param CurrencyDetectorInterface $currencyDetector
     * @param ProductFinderInterface    $productFinder
     */
    public function __construct($name, EngineInterface $templating, RegistryInterface $registry, CurrencyDetectorInterface $currencyDetector, ProductFinderInterface $productFinder)
    {
        $this->productRepository = $registry->getManager()->getRepository('Application\Sonata\ProductBundle\Entity\Product');
        $this->currencyDetector  = $currencyDetector;
        $this->productFinder     = $productFinder;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$product = $this->getProductRepository()->findOneBy(array('id' => $blockContext->getSetting('base_product_id')))) {
            return;
        }

        $products = $this->getProductFinder()->getCrossSellingSimilarParentProducts($product, $blockContext->getSetting('number'));

        $params = array(
            'context'   => $blockContext,
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
            'products'  => $products,
            'currency'  => $this->currencyDetector->getCurrency(),
        );

        return $this->renderResponse($blockContext->getTemplate(), $params, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('number',          'integer', array('required' => true)),
                array('title',           'text',    array('required' => false)),
                array('base_product_id', 'integer', array('required' => false)),
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Similar products';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'number'          => 5,
            'title'           => 'Similar products',
            'base_product_id' => null,
            'template'        => 'SonataProductBundle:Block:similar_products.html.twig'
        ));
    }

    /**
     * Returns the Base ProductRepository.
     *
     * @return BaseProductRepository
     */
    protected function getProductRepository()
    {
        return $this->productRepository;
    }

    /**
     * Returns the Product finder.
     *
     * @return ProductFinderInterface
     */
    protected function getProductFinder()
    {
        return $this->productFinder;
    }
}
