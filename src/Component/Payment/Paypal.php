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

namespace Sonata\Component\Payment;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * A free delivery method, used this only for testing.
 */
class Paypal extends BasePaypal
{
    // PayPal pending reason
    // From PP_OrderManagement_IntegrationGuide, March 2008 p58
    public const PENDING_REASON_ADDRESS = 'address';
    public const PENDING_REASON_AUTHORIZATION = 'authorization';
    public const PENDING_REASON_ECHECK = 'echeck';
    public const PENDING_REASON_INTL = 'intl';
    public const PENDING_REASON_MULTICURRENCY = 'multi-currency';
    public const PENDING_REASON_UNILATERAL = 'unilateral';
    public const PENDING_REASON_UPGRADE = 'upgrade';
    public const PENDING_REASON_VERIFY = 'verify';
    public const PENDING_REASON_OTHER = 'other';

    public function sendbank(OrderInterface $order)
    {
        $params = [
            'order' => $order->getReference(),
            'bank' => $this->getCode(),
            'check' => $this->generateUrlCheck($order),
        ];

        $fields = [
            // paypal specific
            'cmd' => '_xclick',
            'charset' => 'utf-8',
            'business' => $this->getOption('account'),
            'cert_id' => $this->getOption('cert_id'),
            'no_shipping' => '1', // client cannot add shipping address
            'lc' => 'EN', // user interface language
            'no_note' => '1', // no comment on paypal

            // invoice information
            'invoice' => $order->getReference(),
            'amount' => $order->getTotalInc(),
            'currency_code' => $order->getCurrency(),
            'item_name' => 'Order '.$order->getReference(),
            'bn' => 'Sonata/1.0', // Assign Build Notation for PayPal Support

            // user information, for prepopulated form (paypal side)
            'first_name' => $order->getBillingName(),
            'last_name' => '',
            'address1' => $order->getBillingAddress1(),
            'address2' => $order->getBillingAddress2(),
            'city' => $order->getBillingCity(),
            'zip' => $order->getBillingPostcode(),
            'country' => $order->getBillingCountryCode(),

            // Callback information
            'custom' => $this->generateUrlCheck($order),
            'notify_url' => $this->router->generate($this->getOption('url_callback'), $params, UrlGeneratorInterface::ABSOLUTE_URL),

            // user link
            'cancel_return' => $this->router->generate($this->getOption('url_return_ko'), $params, UrlGeneratorInterface::ABSOLUTE_URL),
            'return' => $this->router->generate($this->getOption('url_return_ok'), $params, UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        if ($this->getOption('debug', false)) {
            $html = '<html><body>'."\n";
        } else {
            $html = '<html><body onload="document.getElementById(\'submit_button\').disabled = \'disabled\'; document.getElementById(\'formPaiement\').submit();">'."\n";
        }

        $method = $this->getOption('method', 'encrypt');

        $html .= sprintf('<form action="%s" method="%s" id="formPaiement" >'."\n", $this->getOption('url_action'), 'POST');
        $html .= '<input type="hidden" name="cmd" value="_s-xclick">'."\n";
        $html .= sprintf('<input type="hidden" name="encrypted" value="%s" />', \call_user_func([$this, $method], $fields));

        $html .= '<p>'.$this->translator->trans('process_to_paiement_bank_page', [], 'PaymentBundle').'</p>';
        $html .= '<input type="submit" id="submit_button" value="'.$this->translator->trans('process_to_paiement_btn', [], 'PaymentBundle').'" />';
        $html .= '</form>';

        $html .= '</body></html>';

        if ($this->getOption('debug', false)) {
            echo "<!-- Encrypted Array : \n".print_r($fields, 1).'-->';
        }

        $response = new Response($html, 200, [
            'Content-Type' => 'text/html',
        ]);
        $response->setPrivate(true);

        return $response;
    }

    /**
     * Default encryption method.
     *
     * @param $hash
     *
     * @return string
     */
    public function encrypt($hash)
    {
        return $this->encryptViaBuffer($hash);
    }

    /**
     * From paypal documentation:.
     *
     * 1. A customer payment or a refund triggers IPN. This payment can be via Website Payments
     * Standard FORMs or via the PayPal Web Services APIs for Express Checkout, MassPay, or
     * RefundTransaction. If the payment has a “Pending” status, you receive another IPN when
     * the payment clears, fails, or is denied.
     *
     * 2. PayPal posts HTML FORM variables to a program at a URL you specify. You can specify
     * this URL either in your Profile or with the notify_url variable on each transaction. This
     * post is the heart of IPN. Included in the notification is the customer’s payment information
     * (such as customer name, payment amount). All possible variables in IPN posts are detailed
     * in . When your server receives a notification, it must process the incoming data.
     *
     * 3. Your server must then validate the notification to ensure that it is legitimate.
     *
     *
     * {@inheritdoc}
     */
    public function isCallbackValid(TransactionInterface $transaction)
    {
        $order = $transaction->getOrder();

        if (!$this->isRequestValid($transaction)) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);

            return false;
        }

        if ($order->isValidated()) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);

            return false;
        }

        if ('Pending' === $transaction->get('payment_status')) {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_PENDING);

            return true;
        }

        if ('Completed' === $transaction->get('payment_status')) {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);

            return true;
        }

        if ('Cancelled' === $transaction->get('payment_status')) {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_CANCELLED);

            return true;
        }

        $transaction->setState(TransactionInterface::STATE_KO);
        $transaction->setStatusCode(TransactionInterface::STATUS_UNKNOWN);

        return false;
    }

    public function handleError(TransactionInterface $transaction): void
    {
        $order = $transaction->getOrder();

        switch ($transaction->getStatusCode()) {
            case TransactionInterface::STATUS_ORDER_UNKNOWN:

                if ($this->getLogger()) {
                    $this->getLogger()->emergency('[Paypal:handlerError] ERROR_ORDER_UNKNOWN');
                }

                break;
            case TransactionInterface::STATUS_ERROR_VALIDATION:

                if ($this->getLogger()) {
                    $this->getLogger()->emergency(sprintf('[Paypal:handlerError] STATUS_ERROR_VALIDATION - Order %s - Paypal reject the postback validation', $order->getReference()));
                }

                break;

            case TransactionInterface::STATUS_CANCELLED:
                // cancelled
                $order->setStatus(OrderInterface::STATUS_CANCELLED);

                if ($this->getLogger()) {
                    $this->getLogger()->emergency(sprintf('[Paypal:handlerError] STATUS_CANCELLED - Order %s - The Order has been cancelled, see callback dump for more information', $order->getReference()));
                }

                break;

            case TransactionInterface::STATUS_PENDING:
                // pending
                $order->setStatus(OrderInterface::STATUS_PENDING);

                if ($this->getLogger()) {
                    $reasons = self::getPendingReasonsList();
                    $this->getLogger()->emergency(sprintf('[Paypal:handlerError] STATUS_PENDING - Order %s - reason code : %s - reason : %s', $order->getReference(), $reasons[$transaction->get('pending_reason')], $transaction->get('pending_reason')));
                }

                break;
            default:

                if ($this->getLogger()) {
                    $this->getLogger()->emergency(sprintf('[Paypal:handlerError] STATUS_PENDING - uncaught error'));
                }
        }

        $transaction->setState(TransactionInterface::STATE_KO);

        if (null === $order->getStatus()) {
            $order->setStatus(OrderInterface::STATUS_CANCELLED);
        }

        if (null === $transaction->getStatusCode()) {
            $transaction->setStatusCode(TransactionInterface::STATUS_UNKNOWN);
        }
    }

    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        if (!$transaction->isValid()) {
            return new Response('');
        }

        $params = $transaction->getParameters();
        $params['cmd'] = '_notify-validate';
        //$this->getLogger()->
        // retrieve the client
        $client = $this
            ->getWebConnectorProvider()
            ->getNamedClient($this->getOption('web_connector_name', 'default'));

        $client->request('POST', $this->getOption('url_action'), $params);

        if ('VERIFIED' === $client->getResponse()->getContent()) {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);

            $transaction->getOrder()->setValidatedAt(new \DateTime());
            $transaction->getOrder()->setStatus(OrderInterface::STATUS_VALIDATED);
            $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_VALIDATED);
        } else {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_ERROR_VALIDATION);

            // TODO error in status -> setting payment status to an order status value
            $transaction->getOrder()->setPaymentStatus(OrderInterface::STATUS_ERROR);

            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Paypal::sendAccuseReception] Paypal failed to check the postback');
            }
        }

        return new Response('');
    }

    public function isBasketValid(BasketInterface $basket)
    {
        if (0 === $basket->countBasketElements()) {
            return false;
        }

        foreach ($basket->getBasketElements() as $element) {
            $product = $element->getProduct();
            if (true === $product->isRecurrentPayment()) {
                return false;
            }
        }

        return true;
    }

    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        if (!$product->isRecurrentPayment()) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getPendingReasonsList()
    {
        return [
            self::PENDING_REASON_ADDRESS => 'The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set yo allow you to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.',
            self::PENDING_REASON_AUTHORIZATION => 'You set <PaymentAction> Authorization</PaymentAction> on SetExpressCheckoutRequest and have not yet captured funds.',
            self::PENDING_REASON_ECHECK => 'The payment is pending because it was made by an eCheck that has not yet cleared. ',
            self::PENDING_REASON_INTL => 'The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.',
            self::PENDING_REASON_MULTICURRENCY => 'You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.',
            self::PENDING_REASON_UNILATERAL => 'The payment is pending because it was made to an email address that is not yet registered or confirmed. upgrade: The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status in order to receive the funds. upgrade can also mean that you have reached the monthly limit for transactions on your account. ',
            self::PENDING_REASON_UPGRADE => 'The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status in order to receive the funds. upgrade can also mean that you have reached the monthly limit for transactions on your account.',
            self::PENDING_REASON_VERIFY => 'The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. ',
            self::PENDING_REASON_OTHER => 'The payment is pending for a reason other than those listed above. For more information, contact PayPal Customer Service.',
        ];
    }
}
