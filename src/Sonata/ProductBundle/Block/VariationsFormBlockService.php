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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Component\Form\Type\VariationChoiceType;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;


/**
 * Class VariationsFormBlockService
 *
 * @package Sonata\ProductBundle\Block
 *
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
     * @param string          $name
     * @param EngineInterface $templating
     * @param Pool            $productPool
     */
    public function __construct($name, EngineInterface $templating, Pool $productPool, FormFactoryInterface $formFactory)
    {
        parent::__construct($name, $templating);

        $this->pool = $productPool;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $product = $blockContext->getSetting('product');

        if (null === $product) {
            return $this->renderResponse($blockContext->getTemplate(), array(
                    'context'  => $blockContext,
                    'settings' => $blockContext->getSettings(),
                    'block'    => $blockContext->getBlock(),
                    'choices'  => array(),
                    'form'     => null
                ), $response);
        }

        $fields  = $blockContext->getSetting('variations_properties');

        $choices = $this->pool->getProvider($product)->getVariationsChoices($product, $fields);

        $accessor = PropertyAccess::createPropertyAccessor();

        $currentValues = array();

        foreach ($choices as $field => $values) {
            $currentValues[$field] = array_search($accessor->getValue($product, $field), $values);
        }

        $form = $this->formFactory->createBuilder('sonata_product_variation_choices', $currentValues, array(
                'field_options' => $blockContext->getSetting('form_field_options'),
                'product'       => $product,
                'fields'        => $fields
            ))->getForm();

        $params = array(
            'context'   => $blockContext,
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
            'choices'   => $choices,
            'form'      => $form->createView(),
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
    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        // TODO: Implement buildEditForm() method.
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Products Variations choice';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'product'               => null,
            'variations_properties' => array(),
            'form_route'            => 'sonata_product_variation_product',
            'form_route_parameters' => function (Options $options) {
                    $product = $options->get('product');

                    if (null !== $product && !$product instanceof ProductInterface) {
                        throw new \RuntimeException("Wrong 'product' parameter");
                    }

                    return array(
                        'productId' => $product ? $product->getId() : null,
                        'slug'      => $product ? $product->getSlug() : null
                    );
                },
            'form_field_options'    => array(),
            'title'                 => 'Product variations',
            'template'              => 'SonataProductBundle:Block:variations_choice.html.twig'
        ));
    }
}