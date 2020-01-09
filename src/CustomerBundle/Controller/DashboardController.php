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

namespace Sonata\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

final class DashboardController extends Controller
{
    public function dashboardAction(): Response
    {
        return $this->render('@SonataCustomer/Profile/dashboard.html.twig', [
            'blocks' => $this->container->getParameter('sonata.customer.profile.blocks'),
        ]);
    }
}
