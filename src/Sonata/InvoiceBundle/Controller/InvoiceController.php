<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InvoiceController extends Controller
{
    /**
     * @throws \RuntimeException
     */
    public function indexAction()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @param  string            $reference
     * @throws \RuntimeException
     */
    public function viewAction($reference)
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @param  string            $reference
     * @throws \RuntimeException
     */
    public function downloadAction($reference)
    {
        throw new \RuntimeException('not implemented');
    }
}
