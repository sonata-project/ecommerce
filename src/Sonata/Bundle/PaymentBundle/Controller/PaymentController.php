<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\Component\Payment\Transaction;

class PaymentController extends Controller
{

    public function errorAction() {

    }

    public function confirmationAction() {

    }
    
    /**
     *
     * this action redirect the user to the bank
     *
     * @return void
     */
    public function callbankAction()
    {

        $basket     = $this->container->get('sonata.basket');
        $request    = $this->container->get('request');
        $user       = $this->container->get('user');

        if($request->getMethod() !== 'POST') {
            $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        if(!$basket->isValid()) {
            $this->redirect($this->generateUrl('sonata_basket_index'));
        }
        
        $payment = $basket->getPaymentMethod();

        // check if the basket is valid/compatible with the bank gateway
        if (!$payment->isBasketValid($basket)) {

            $this->container->get('session')->setFlash('notice', $this->containe->get('translator')->trans('basket_not_valid_with_current_payment_method', array(), 'sonata_payment'));

            $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        // transform the basket into order
        $order = $payment->getTransformer('basket')->transformIntoOrder($user, $basket);

        // save the order
        $em = $this->container->getDoctrine_Orm_EntityManagerService(); // todo : find a way to know which EM is linked to the order
        $em->persist($order);
        $em->flush();

        $basket->reset();
        
        // the payment must handle everything when calling the bank
        return $payment->callbank($order);
    }

    /**
     * this action handler the callback sent from the bank
     *
     * @return void
     */
    public function callbackAction()
    {
        
        $request    = $this->container->get('request');

        $bank = $request->getParameter('bank');

        $payment = $this->container->get(sprintf('sonata.payment.%s', $bank));

        $transaction = new Transaction;
        
        //  merge the get and post request, post > get
        $transaction->setParameters($request->query->add($request->request->all()));
        
        $reference = $payment->getOrderReference($transaction);

        // get the order from the database
        $em = $this->container->getDoctrine_Orm_EntityManagerService(); // todo : find a way to know which EM is linked to the order

        $transaction->setOrder($em->getRepository('OrderBundle::Order')->findOneByReference($reference));

        $response = $payment->callback($transaction);

        $em->persist($transaction->getOrder());
        $em->flush();

        // todo : persist the transaction element ....

        return $response;
    }

}