<?php

namespace Sonata\Tests\Component\Form\Type;

use Sonata\Component\Form\Type\BaseVariationFormType;

final class ProductVariationFormTypeTest extends BaseVariationFormType
{
    public function getName() { return 'test'; }
    public function getChoicesForVariation($name) { return array($name); }
    public function getVariationFields() { return array('field_1', 'field2'); }
}


class BaseVariationFormTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertEquals('test', $this->getFormMock()->getName());
    }

    public function testBuildForm()
    {
        $formTypeMock = $this->getMockBuilder('Sonata\Component\Form\Type\BaseVariationFormType')->disableOriginalConstructor()->getMock();
        $formTypeMock->expects($this->any())
            ->method('getVariationFields')
            ->will($this->returnValue(array('field_1', 'field_2')));
        $formBuilderMock = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $formTypeMock->buildForm($formBuilderMock, array());
    }

    public function testCleanDataWithoutTimestamp()
    {
        $class = new \ReflectionClass('Sonata\Tests\Component\Form\Type\ProductVariationFormTypeTest');
        $method = $class->getMethod('cleanData');
        $method->setAccessible(true);

        $input = array('test2', 'test1');
        $method->invokeArgs($this->getFormMock(), array(&$input));
        $expected = array('test1' => 'test1', 'test2' => 'test2');

        $this->assertEquals($expected, $input);
    }

    /**
     * @return ProductVariationFormTypeTest
     */
    protected function getFormMock()
    {
        $productManagerMock = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $dateTimeHelper = $this->getMockBuilder('Sonata\IntlBundle\Templating\Helper\DateTimeHelper')->disableOriginalConstructor()->getMock();

        return new ProductVariationFormTypeTest($productManagerMock, $dateTimeHelper);
    }
}