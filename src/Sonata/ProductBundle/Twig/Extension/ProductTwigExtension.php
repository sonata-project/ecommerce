<?php

namespace Sonata\ProductBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ProductTwigExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Sonata\IntlBundle\Templating\Helper\DateTimeHelper
     */
    protected $dateTimeHelper;

    /**
     * @param RouterInterface $router
     * @param DateTimeHelper $dateTimeHelper
     */
    public function __construct(RouterInterface $router, DateTimeHelper $dateTimeHelper)
    {
        $this->router = $router;
        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sonata_product_extension';
    }

    public function getFunctions()
    {
        return array(
            'sonata_product_jstree' => new \Twig_Function_Method($this, 'jsonTreeBuilder'),
        );
    }


    /**
     * @param ProductProviderInterface $provider
     * @param ProductInterface         $product
     *
     * @return string
     */
    public function jsonTreeBuilder(ProductProviderInterface $provider, ProductInterface $product)
    {
        if (!$provider->hasEnabledVariations($product)) {
            return '{}';
        }

        $return = array();
        $variations = $product->getVariations();
        $nbItems = count($variations);
        $previousPropertyName = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($provider->getVariationFields($product) as $field) {
            for ($j = 0; $j < $nbItems; ++$j) {
                $variation = $variations[$j];

                $value = $accessor->getValue($variation, $field);
                $value = $this->formatTimestampProperty($value);

                if (!isset($return[$field][$value])) {
                    $return[$field][$value]['uri'] = array();
                }

                $productUri = $this->router->generate('sonata_product_view', array(
                    'productId' => $variation->getId(),
                    'slug'      => $variation->getSlug(),
                ));
                $return[$field][$value]['uri'][] = $productUri;

                if ($previousPropertyName) {
                    $previousValue = $accessor->getValue($variation, $previousPropertyName);
                    $previousValue = $this->formatTimestampProperty($previousValue);
                    $return[$previousPropertyName][$previousValue][$field] = &$return[$field][$value];
                }
            }

            $previousPropertyName = $field;
        }

        return json_encode($return);
    }

    /**
     * @param mixed          $input
     *
     * @return string
     */
    protected function formatTimestampProperty($input)
    {
        if ($input instanceof \DateTime) {
            $input = $this->dateTimeHelper->formatDateTime($input);
        }

        return $input;
    }
}