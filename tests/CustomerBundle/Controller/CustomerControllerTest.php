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

namespace Sonata\CustomerBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Sonata\CustomerBundle\Controller\CustomerController;

class CustomerControllerTest extends TestCase
{
    /**
     * @var CustomerController
     */
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new CustomerController();
    }

    public function testItIsInstantiable(): void
    {
        $this->assertNotNull($this->controller);
    }
}
