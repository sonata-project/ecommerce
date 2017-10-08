<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle\Tests\DependencyInjection;

use Sonata\PaymentBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), []);

        $this->validateClass($config);
        $this->validateTransformers($config);

        $this->assertSame('sonata.payment.selector.simple', $config['selector']);
        $this->assertSame('sonata.payment.generator.mysql', $config['generator']);

        $this->assertArrayHasKey('methods', $config);
        $this->assertEmpty($config['methods']);
    }

    public function validateClass($config)
    {
        $this->assertSame([
            'order' => 'Application\\Sonata\\OrderBundle\\Entity\\Order',
            'transaction' => 'Application\\Sonata\\PaymentBundle\\Entity\\Transaction',
        ], $config['class']);
    }

    public function validateTransformers($config)
    {
        $this->assertArrayHasKey('transformers', $config);
        $this->assertSame('sonata.payment.transformer.order', $config['transformers']['order']);
        $this->assertSame('sonata.payment.transformer.basket', $config['transformers']['basket']);
    }
}
