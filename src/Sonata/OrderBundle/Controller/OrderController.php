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
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OrderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws AccessDeniedHttpException
     */
    public function indexAction()
    {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $orders = $this->getOrderManager()->findForUser($user);

        return $this->render('SonataOrderBundle:Order:index.html.twig', array(
            'orders' => $orders
        ));
    }

    /**
     * @param string $reference
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws AccessDeniedHttpException
     */
    public function viewAction($reference)
    {
        $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

        if (null === $order) {
            throw new AccessDeniedHttpException();
        }

        $this->checkAccess($order->getCustomer());

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
     * Checks that the current logged in user has access to given invoice
     *
     * @param CustomerInterface $customer The linked customer
     *
     * @throws AccessDeniedHttpException
     */
    protected function checkAccess(CustomerInterface $customer)
    {
        if (!($user = $this->getUser())
        || !$customer->getUser()
        || $customer->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @return OrderManagerInterface
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }
}
