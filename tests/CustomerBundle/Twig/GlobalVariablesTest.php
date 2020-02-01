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

namespace Sonata\CustomerBundle\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sonata\CustomerBundle\Twig\GlobalVariables;

/**
 * @author Wojciech Błoszyk <wbloszyk@gmail.com>
 */
class GlobalVariablesTest extends TestCase
{
    /**
     * @var GlobalVariables
     */
    private $globalVariables;

    protected function setUp(): void
    {
        $this->globalVariables = new GlobalVariables('@SonataCustomer/Profile/action.html.twig');
    }

    public function testGetProfileTemplate(): void
    {
        $this->assertSame('@SonataCustomer/Profile/action.html.twig', $this->globalVariables->getProfileTemplate());
    }
}
