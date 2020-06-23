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

namespace Sonata\ProductBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\ProductBundle\DependencyInjection\SonataProductExtension;
use Twig\Extra\String\StringExtension;

class SonataProductExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', []);
    }

    public function testLoadTwigStringExtension(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(StringExtension::class, 'twig.extension');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new SonataProductExtension(),
        ];
    }
}
