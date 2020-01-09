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

namespace Sonata\CustomerBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\CustomerBundle\DependencyInjection\Configuration;
use Sonata\CustomerBundle\DependencyInjection\SonataCustomerExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class SonataCustomerExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', ['SonataCustomerBundle' => true]);
    }

    public function testAliases(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias(
            'sonata.customer.profile.menu_builder',
            'sonata.customer.profile.menu_builder.default'
        );
    }

    public function testEmptyProfileMenu(): void
    {
        $this->load([
            'profile' => [
                'menu' => null,
            ],
        ]);
    }

    public function testEmptyProfileBlocks(): void
    {
        $this->load([
            'profile' => [
                'blocks' => null,
            ],
        ]);
    }

    protected function getMinimalConfiguration(): array
    {
        return (new Processor())->process((new Configuration())->getConfigTreeBuilder()->buildTree(), []);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataCustomerExtension(),
        ];
    }

    protected function load(array $configurationValues = []): void
    {
        $configs = [$this->getMinimalConfiguration(), $configurationValues];

        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }

            $extension->load($configs, $this->container);
        }
    }
}
