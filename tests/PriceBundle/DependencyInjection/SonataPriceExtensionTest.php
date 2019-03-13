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

namespace Sonata\PriceBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonata\PriceBundle\DependencyInjection\SonataPriceExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class SonataPriceExtensionTest extends TestCase
{
    /**
     * Tests if the configuration is well parsed & parameters are well set.
     */
    public function testConfiguration(): void
    {
        $configuration = new ContainerBuilder();
        $loader = new SonataPriceExtension();
        $config = $this->getDefaultConfig();

        $loader->load($config, $configuration);
        $this->assertTrue($configuration instanceof ContainerBuilder);

        $this->assertTrue($configuration->hasParameter('sonata.price.currency'));
        $this->assertSame('EUR', $configuration->getParameter('sonata.price.currency'));
    }

    /**
     * Asserts that an InvalidConfigurationException is thrown when not providing currency parameter.
     */
    public function testCurrencyRequired(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $configuration = new ContainerBuilder();
        $loader = new SonataPriceExtension();
        $config = $this->getDefaultConfig();

        unset($config[0]['currency']);
        $loader->load($config, $configuration);
    }

    /**
     * Gets the configuration as an array.
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [[
            'currency' => 'EUR',
        ]];
    }
}
