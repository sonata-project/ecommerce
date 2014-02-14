<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\Component\Generator;

use Sonata\Component\Generator\Mustache;

/**
 * Class MustacheTest
 *
 * @package Sonata\Test\Component\Generator
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class MustacheTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderString()
    {
        $testInput = "{{ replace }}.42 toubidou {{ second }}";

        $expectedOutput = "abc.42 toubidou def";

        $this->assertEquals($expectedOutput, Mustache::renderString($testInput, array('replace' => "abc", 'second' => "def")));
    }
}
