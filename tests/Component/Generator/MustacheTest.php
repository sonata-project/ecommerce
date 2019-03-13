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

namespace Sonata\Component\Tests\Generator;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Generator\Mustache;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class MustacheTest extends TestCase
{
    public function testRenderString(): void
    {
        $testInput = '{{ replace }}.42 toubidou {{ second }}';

        $expectedOutput = 'abc.42 toubidou def';

        $this->assertSame($expectedOutput, Mustache::renderString($testInput, ['replace' => 'abc', 'second' => 'def']));
    }
}
