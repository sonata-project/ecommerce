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

namespace Sonata\PaymentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketFactoryInterface;
use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\Component\Payment\PaymentHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PaymentController extends Controller
{
    /**
     * @var BasketFactoryInterface
     */
    private $basketFactory;

    /**
     * @var PaymentHandlerInterface
     */
    private $paymentHandler;

    /**
     * @var Basket
     */
    private $basket;

    public function __construct(BasketFactoryInterface $basketFactory = null, PaymentHandlerInterface $paymentHandler = null, Basket $basket = null)
    {
        if (!$basketFactory) {
            @trigger_error(sprintf(
                'Not providing a %s instance to %s is deprecated since sonata-project/ecommerce 3.x. Providing it will be mandatory in 4.0',
                BasketFactoryInterface::class,
                __METHOD__
            ), E_USER_DEPRECATED);
        }

        if (!$paymentHandler) {
            @trigger_error(sprintf(
                'Not providing a %s instance to %s is deprecated since sonata-project/ecommerce 3.x. Providing it will be mandatory in 4.0',
                PaymentHandlerInterface::class,
                __METHOD__
            ), E_USER_DEPRECATED);
        }

        if (!$basket) {
            @trigger_error(sprintf(
                'Not providing a %s instance to %s is deprecated since sonata-project/ecommerce 3.x. Providing it will be mandatory in 4.0',
                Basket::class,
                __METHOD__
            ), E_USER_DEPRECATED);
        }

        $this->basketFactory = $basketFactory;
        $this->paymentHandler = $paymentHandler;
        $this->basket = $basket;
    }

    /**
     * This action is called by the user after the sendbank
     * In most case the order is already cancelled by a previous callback.
     *
     * @throws UnauthorizedHttpException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function errorAction(Request $request)
    {
        try {
            $order = $this->getPaymentHandler()->handleError($request, $this->getBasket());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $this->render('@SonataPayment/Payment/error.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     *
     * @return Response
     */
    public function confirmationAction(Request $request)
    {
        try {
            $order = $this->getPaymentHandler()->handleConfirmation($request);
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        if (!($order->isValidated() || $order->isPending())) {
            return $this->render('@SonataPayment/Payment/error.html.twig', [
                'order' => $order,
            ]);
        }

        return $this->render('@SonataPayment/Payment/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * this action redirect the user to the bank.
     *
     * @return Response
     */
    public function sendbankAction(Request $request)
    {
        $basket = $this->getBasket();

        if ('POST' !== $request->getMethod()) {
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        if (!$basket->isValid()) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid', [], 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $payment = $basket->getPaymentMethod();

        // check if the basket is valid/compatible with the bank gateway
        if (!$payment->isBasketValid($basket)) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid_with_current_payment_method', [], 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        // transform the basket into order
        $order = $this->getPaymentHandler()->getSendbankOrder($basket);
        $this->getBasketFactory()->reset($basket);

        // the payment must handle everything when calling the bank
        return $payment->sendbank($order);
    }

    /**
     * this action handler the callback sent from the bank.
     *
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     *
     * @return Response
     */
    public function callbackAction(Request $request)
    {
        try {
            $response = $this->getPaymentHandler()->getPaymentCallbackResponse($request);
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function termsAction()
    {
        return $this->render('@SonataPayment/Payment/terms.html.twig');
    }

    /**
     * @return BasketFactoryInterface
     */
    protected function getBasketFactory()
    {
        if ($this->basketFactory instanceof BasketFactoryInterface) {
            return $this->basketFactory;
        }

        return $this->get('sonata.basket.factory');
    }

    /**
     * @return Basket
     */
    protected function getBasket()
    {
        if ($this->basket instanceof Basket) {
            return $this->basket;
        }

        return $this->get('sonata.basket');
    }

    /**
     * @return PaymentHandlerInterface
     */
    protected function getPaymentHandler()
    {
        if ($this->paymentHandler instanceof PaymentHandlerInterface) {
            return $this->paymentHandler;
        }

        return $this->get('sonata.payment.handler');
    }
}
