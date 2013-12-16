<?php

namespace Sonata\Tests\Component\Product;

use Sonata\Component\Product\ProductManagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Form\Type\VariationFormTypeInterface;

class VariationFormTypeTest implements VariationFormTypeInterface
{
    public function __construct(ProductManagerInterface $manager, DateTimeHelper $dateTimeHelper) { }
    public function getChoicesForVariation($name) { return array(); }
    public function getVariationFields() { return array(); }
}

class ProductDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFormType()
    {
        $productProvider = $this->getMockBuilder('Sonata\Component\Product\ProductProviderInterface')->disableOriginalConstructor()->getMock();
        $productManager = $this->getMockBuilder('Sonata\Component\Product\ProductManagerInterface')->disableOriginalConstructor()->getMock();
        $dateTimeHelper = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();
        $formType = new VariationFormTypeTest($productManager, $dateTimeHelper);

        $productDefinition = new ProductDefinition($productProvider, $productManager, $formType);

        $this->assertTrue($productDefinition->getVariationFormType() instanceof VariationFormTypeTest);
    }
}