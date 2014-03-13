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

use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Customer\CustomerInterface;

class OrderController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws AccessDeniedException
     */
    public function indexAction()
    {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        $orders = $this->getOrderManager()->findForUser($user, array('createdAt' => "DESC"));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('order_index_title', array(), "SonataOrderBundle"));

        return $this->render('SonataOrderBundle:Order:index.html.twig', array(
            'orders'             => $orders,
            'breadcrumb_context' => 'user_order',
        ));
    }

    /**
     * @param string $reference
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws AccessDeniedException
     */
    public function viewAction($reference)
    {
        /** @var OrderInterface $order */
        $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

        if (null === $order) {
            throw new AccessDeniedException();
        }

        $this->checkAccess($order->getCustomer());

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('order_view_title', array(), "SonataOrderBundle"));

        /** @var OrderElementInterface $element */
        foreach ($order->getOrderElements() as $element) {
            $provider = $this->get('sonata.product.pool')->getProvider($element->getProductType());
            $element->setProduct($provider->getProductFromRaw($element, $this->get('sonata.product.pool')->getManager($element->getProductType())->getClass()));
        }

        return $this->render('SonataOrderBundle:Order:view.html.twig', array(
            'order'              => $order,
            'breadcrumb_context' => 'user_order',
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
     * @throws AccessDeniedException
     */
    protected function checkAccess(CustomerInterface $customer)
    {
        if (!($user = $this->getUser())
        || !$customer->getUser()
        || $customer->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedException();
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
