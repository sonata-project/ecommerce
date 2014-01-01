<?php

namespace Sonata\Test\ProductBundle\Twig\Extension;

use Sonata\ProductBundle\Entity\BaseProduct;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\ProductBundle\Twig\Extension\ProductExtension;
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


class ProductExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $extension = $this->getExtensionMock();

        $this->assertEquals('sonata_product', $extension->getName());
    }

    public function testJsonTreeBuilderNoVariation()
    {
        $poolMock = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $dateTimeHelperMock = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();
        $extension = $this->getMock('Sonata\ProductBundle\Twig\Extension\ProductExtension', array('getProductProvider'), array(
            $poolMock,
            $routerMock,
            $dateTimeHelperMock
        ));

        $providerMock = $this->getMockBuilder('Sonata\Component\Product\ProductProviderInterface')->disableOriginalConstructor()->getMock();
        $extension->expects($this->any())
            ->method('getProductProvider')
            ->will($this->returnValue($providerMock));
        $providerMock->expects($this->once())
            ->method('hasEnabledVariations')
            ->will($this->returnValue(false));

        $productMock = new ProductTest();

        $result = $extension->jsonTreeBuilder($productMock);

        $this->assertEquals(array(), $result);
    }

    public function testJsonTreeBuilderEmptyResult()
    {
        $poolMock = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $dateTimeHelperMock = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();
        $extension = $this->getMock('Sonata\ProductBundle\Twig\Extension\ProductExtension', array('getProductProvider'), array(
            $poolMock,
            $routerMock,
            $dateTimeHelperMock
        ));

        $providerMock = $this->getMockBuilder('Sonata\Component\Product\ProductProviderInterface')->disableOriginalConstructor()->getMock();
        $extension->expects($this->any())
            ->method('getProductProvider')
            ->will($this->returnValue($providerMock));
        $providerMock->expects($this->once())
            ->method('hasEnabledVariations')
            ->will($this->returnValue(true));
        $providerMock->expects($this->any())
            ->method('getVariationFields')
            ->will($this->returnValue(array()));

        $productMock = new ProductTest();

        $result = $extension->jsonTreeBuilder($productMock);

        $this->assertEquals(array(), $result);
    }

    public function testJsonTreeBuilder()
    {
        $poolMock = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $routerMock->expects($this->any())
            ->method('generate')
            ->will($this->returnValue('my/test/url'));
        $dateTimeHelperMock = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();
        $extension = $this->getMock('Sonata\ProductBundle\Twig\Extension\ProductExtension', array('getProductProvider'), array(
            $poolMock,
            $routerMock,
            $dateTimeHelperMock
        ));

        $providerMock = $this->getMockBuilder('Sonata\Component\Product\ProductProviderInterface')->disableOriginalConstructor()->getMock();
        $extension->expects($this->any())
            ->method('getProductProvider')
            ->will($this->returnValue($providerMock));
        $providerMock->expects($this->once())
            ->method('hasEnabledVariations')
            ->will($this->returnValue(true));
        $providerMock->expects($this->any())
            ->method('getVariationFields')
            ->will($this->returnValue(array('test_field')));

        $productMock = new ProductTest();

        $result = $extension->jsonTreeBuilder($productMock);
        $expected = array(
            'test_field' => array(
                'result value' => array(
                    'uri' => array(
                        'my/test/url'
                    )
                )
            )
        );

        $this->assertEquals($expected, $result);
    }

    public function testFormatTimestampPropertyWithAnythingButDateTime()
    {
        $class = new \ReflectionClass('Sonata\ProductBundle\Twig\Extension\ProductExtension');
        $method = $class->getMethod('formatTimestampProperty');
        $method->setAccessible(true);
        $extension = $this->getExtensionMock();

        $result = $method->invokeArgs($extension, array('LOLTESTING'));

        $this->assertEquals('LOLTESTING', $result);
    }

    public function testFormatTimestampProperty()
    {
        $class = new \ReflectionClass('Sonata\ProductBundle\Twig\Extension\ProductExtension');
        $method = $class->getMethod('formatTimestampProperty');
        $method->setAccessible(true);
        $extension = $this->getExtensionMock();

        $result = $method->invokeArgs($extension, array('LOLTESTING'));

        $this->assertEquals('LOLTESTING', $result);
    }

    /**
     * Generate a new ProductExtension using mocks as parameter
     *
     * @return ProductExtension
     */
    protected function getExtensionMock()
    {
        $poolMock = $this->getMockBuilder('Sonata\Component\Product\Pool')->disableOriginalConstructor()->getMock();
        $routerMock = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->disableOriginalConstructor()->getMock();
        $dateTimeHelperMock = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();

        return new ProductExtension($poolMock, $routerMock, $dateTimeHelperMock);
    }
}