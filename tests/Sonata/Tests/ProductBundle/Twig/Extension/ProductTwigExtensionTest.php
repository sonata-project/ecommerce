<?php

namespace Sonata\Test\ProductBundle\Twig\Extension;

use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\ProductBundle\Twig\Extension\ProductTwigExtension;
use Sonata\Component\Product\ProductInterface;

final class ProductProviderTest extends BaseProductProvider
{
    public function getBaseControllerName() { }
    public function getVariationFields() { return array('test_field'); }
    public function hasEnabledVariations(ProductInterface $product) { return true; }
}

final class ProductTest extends BaseProduct
{
    public function getId() { }
    public function getTestField() { return 'result value'; }
    public function getVariations() { return array(new self()); }
}


class ProductTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $extension = $this->getExtensionMock();

        $this->assertEquals('sonata_product_extension', $extension->getName());
    }

    public function testGetFunctions()
    {
        $extension = $this->getExtensionMock();
        $functions = $extension->getFunctions();

        $this->assertTrue(isset($functions['sonata_product_jstree']));
    }

    public function testJsonTreeBuilderEmptyResult()
    {
        $extension = $this->getExtensionMock();
        $productProviderMock = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $productProviderMock->expects($this->any())
            ->method('hasEnabledVariations')
            ->will($this->returnValue(false));
        $productMock = $this->getMock('Sonata\Component\Product\ProductInterface');

        $result = $extension->jsonTreeBuilder($productProviderMock, $productMock);

        $this->assertEquals('{}', $result);
    }

    public function testJsonTreeBuilder()
    {
        $extension = $this->getExtensionMock();
        $serializerMock = $this->getMock('JMS\Serializer\SerializerInterface');
        $productProviderMock = new ProductProviderTest($serializerMock);
        $productMock = new ProductTest();

        $result = $extension->jsonTreeBuilder($productProviderMock, $productMock);

        $this->assertEquals(array('test_field' => array('result value' => array('uri' => array(0 => null)))), json_decode($result, true));
    }

    public function testFormatTimestampPropertyWithAnythingButDateTime()
    {
        $class = new \ReflectionClass('Sonata\ProductBundle\Twig\Extension\ProductTwigExtension');
        $method = $class->getMethod('formatTimestampProperty');
        $method->setAccessible(true);
        $extension = $this->getExtensionMock();

        $result = $method->invokeArgs($extension, array('LOLTESTING'));

        $this->assertEquals('LOLTESTING', $result);
    }

    public function testFormatTimestampProperty()
    {
        $class = new \ReflectionClass('Sonata\ProductBundle\Twig\Extension\ProductTwigExtension');
        $method = $class->getMethod('formatTimestampProperty');
        $method->setAccessible(true);
        $extension = $this->getExtensionMock();

        $result = $method->invokeArgs($extension, array('LOLTESTING'));

        $this->assertEquals('LOLTESTING', $result);
    }

    /**
     * Generate a new ProductTwigExtension using mocks as parameter
     *
     * @return ProductTwigExtension
     */
    protected function getExtensionMock()
    {
        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $dateTimeHelperMock = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();

        return new ProductTwigExtension($routerMock, $dateTimeHelperMock);
    }
}