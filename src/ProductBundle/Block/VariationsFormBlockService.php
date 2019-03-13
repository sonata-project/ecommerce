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
use Sonata\Component\Form\Type\VariationChoiceType;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class VariationsFormBlockService extends BaseBlockService
{
    /**
     * @var \Sonata\Component\Product\Pool
     */
    protected $pool;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param string               $name
     * @param EngineInterface      $templating
     * @param Pool                 $productPool
     * @param FormFactoryInterface $formFactory
     */
    public function __construct($name, EngineInterface $templating, Pool $productPool, FormFactoryInterface $formFactory)
    {
        parent::__construct($name, $templating);

        $this->pool = $productPool;
        $this->formFactory = $formFactory;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $product = $blockContext->getSetting('product');

        if (null === $product) {
            return $this->renderResponse($blockContext->getTemplate(), [
                    'context' => $blockContext,
                    'settings' => $blockContext->getSettings(),
                    'block' => $blockContext->getBlock(),
                    'choices' => [],
                    'form' => null,
                ], $response);
        }

        $fields = $blockContext->getSetting('variations_properties');

        $choices = $this->pool->getProvider($product)->getVariationsChoices($product, $fields);

        $accessor = PropertyAccess::createPropertyAccessor();

        $currentValues = [];

        foreach ($choices as $field => $values) {
            $currentValues[$field] = array_search($accessor->getValue($product, $field), $values, true);
        }

        $form = $this->formFactory->createBuilder(VariationChoiceType::class, $currentValues, [
                'field_options' => $blockContext->getSetting('form_field_options'),
                'product' => $product,
                'fields' => $fields,
            ])->getForm();

        $params = [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'choices' => $choices,
            'form' => $form->createView(),
        ];

        return $this->renderResponse($blockContext->getTemplate(), $params, $response);
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
        // TODO: Implement validateBlock() method.
    }

    public function buildEditForm(FormMapper $form, BlockInterface $block): void
    {
        // TODO: Implement buildEditForm() method.
    }

    public function getName()
    {
        return 'Products Variations choice';
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'product' => null,
            'variations_properties' => [],
            'form_route' => 'sonata_product_variation_product',
            'form_route_parameters' => function (Options $options) {
                $product = $options['product'];

                if (null !== $product && !$product instanceof ProductInterface) {
                    throw new \RuntimeException("Wrong 'product' parameter");
                }

                return [
                        'productId' => $product ? $product->getId() : null,
                        'slug' => $product ? $product->getSlug() : null,
                    ];
            },
            'form_field_options' => [],
            'title' => 'Product variations',
            'template' => '@SonataProduct/Block/variations_choice.html.twig',
        ]);
    }
}
