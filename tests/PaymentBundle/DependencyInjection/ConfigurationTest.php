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

namespace Sonata\PaymentBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonata\PaymentBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class ConfigurationTest extends TestCase
{
    public function testDefaults(): void
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

    public function validateClass($config): void
    {
        $this->assertSame([
            'order' => 'App\\Sonata\\OrderBundle\\Entity\\Order',
            'transaction' => 'App\\Sonata\\PaymentBundle\\Entity\\Transaction',
        ], $config['class']);
    }

    public function validateTransformers($config): void
    {
        $this->assertArrayHasKey('transformers', $config);
        $this->assertSame('sonata.payment.transformer.order', $config['transformers']['order']);
        $this->assertSame('sonata.payment.transformer.basket', $config['transformers']['basket']);
    }
}
