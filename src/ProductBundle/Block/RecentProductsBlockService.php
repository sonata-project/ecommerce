<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\ProductBundle\Repository\BaseProductRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class RecentProductsBlockService extends BaseBlockService
{
    /**
     * @var BaseProductRepository
     */
    protected $productRepository;

    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @param string                    $name
     * @param EngineInterface           $templating
     * @param RegistryInterface         $registry
     * @param CurrencyDetectorInterface $currencyDetector
     * @param string                    $productClass
     */
    public function __construct($name, EngineInterface $templating, RegistryInterface $registry, CurrencyDetectorInterface $currencyDetector, $productClass)
    {
        $this->productRepository = $registry->getManager()->getRepository($productClass);
        $this->currencyDetector = $currencyDetector;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $products = $this->getProductRepository()
            ->findLastActiveProducts($blockContext->getSetting('number'));

        $params = [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'products' => $products,
            'currency' => $this->currencyDetector->getCurrency(),
        ];

        return $this->renderResponse($blockContext->getTemplate(), $params, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block): void
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', [
            'keys' => [
                ['number', 'integer', ['required' => true]],
                ['title',  'text',    ['required' => false]],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Recent products';
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'title' => 'Recent products',
            'template' => 'SonataProductBundle:Block:recent_products.html.twig',
        ]);
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
}
