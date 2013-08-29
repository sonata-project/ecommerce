<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\Component\Order\OrderManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    /**
     * @throws \RuntimeException
     */
    public function indexAction()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @param string $reference
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws NotFoundHttpException
     */
    public function viewAction($reference)
    {
        $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

        if (null === $order) {
            throw new NotFoundHttpException("Order with reference ".$reference." could not be found.");
        }

        return $this->render('SonataOrderBundle:Order:view.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @param  unknown           $reference
     * @throws \RuntimeException
     */
    public function downloadAction($reference)
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @return OrderManagerInterface
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }
}
