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

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Resources :.
 *
 * Paypal Encryption
 *  https://www.paypal.com/IntegrationCenter/ic_button-encryption.html#Createanencryptedbutton
 *   openssl genrsa -out my-prvkey.pem 1024
 *   openssl req -new -key my-prvkey.pem -x509 -days 365 -out my-pubcert.pem
 *  *
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class BasePaypal extends BasePayment
{
    public const PAYMENT_STATUS_CANCELED_REVERSAL = 'Canceled_Reversal';
    public const PAYMENT_STATUS_COMPLETED = 'Completed';
    public const PAYMENT_STATUS_DENIED = 'Denied';
    public const PAYMENT_STATUS_FAILED = 'Failed';
    public const PAYMENT_STATUS_PENDING = 'Pending';
    public const PAYMENT_STATUS_REFUNDED = 'Refunded';
    public const PAYMENT_STATUS_REVERSED = 'Reversed';
    public const PAYMENT_STATUS_PROCESSED = 'Processed';
    public const PAYMENT_STATUS_VOIDED = 'Voided';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Object that manages http client.
     *
     * @todo Check if it really is used
     */
    protected $webConnectorProvider = null;

    /**
     * @param \Symfony\Component\Routing\RouterInterface              $router
     * @param \Symfony\Component\Translation\TranslatorInterface|null $translator
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator = null)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * return true if the request contains a valid `check` parameter.
     *
     * @param TransactionInterface $transaction
     *
     * @return bool
     */
    public function isRequestValid(TransactionInterface $transaction)
    {
        $checkUrl = $transaction->get('check');

        $checkPrivate = $this->generateUrlCheck($transaction->getOrder());

        return $checkUrl === $checkPrivate;
    }

    /**
     * return the transaction_id sent by the bank.
     *
     * @param TransactionInterface $transaction
     */
    public function applyTransactionId(TransactionInterface $transaction): void
    {
        $transactionId = $transaction->get('txn_id', null);

        if (!$transactionId) {
            // no transaction id provided
            $transactionId = -1;
        }

        $transaction->setTransactionId($transactionId);
    }

    /**
     * return the order reference from the transaction object.
     *
     * @param TransactionInterface $transaction
     *
     * @return string
     */
    public function getOrderReference(TransactionInterface $transaction)
    {
        $order = $transaction->get('order', null);

        if ($this->getLogger()) {
            $this->getLogger()->notice(sprintf('[BasePaypalPayment::loadOrder] order=%s', $order));
        }

        return $order;
    }

    /**
     * Check openssl configuration, throw an RuntimeException if something is wrong.
     *
     * @throws RuntimeException
     */
    public function checkPaypalFiles(): void
    {
        $key_file = $this->getOption('key_file');
        $cert_file = $this->getOption('cert_file');
        $paypal_cert_file = $this->getOption('paypal_cert_file');
        $openssl = $this->getOption('openssl');

        // key file
        if (!file_exists($key_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency(sprintf('Merchant key file not found : %s', $key_file));
            }

            throw new \RuntimeException(sprintf('Merchant key file not found : %s', $key_file));
        }

        if (!is_readable($key_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('Merchant key file is not readable');
            }

            throw new \RuntimeException('Merchant key file is not readable');
        }

        // cert file
        if (!file_exists($cert_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('Merchant certificate file not found');
            }

            throw new \RuntimeException('Merchant certificate file not found');
        }

        if (!is_readable($cert_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('Merchant certificate file is not readable');
            }

            throw new \RuntimeException('Merchant certificate file is not readable');
        }

        // paypal cert file
        if (!file_exists($paypal_cert_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('PayPal certificate file not found');
            }

            throw new \RuntimeException('PayPal certificate file not found');
        }

        if (!is_readable($cert_file)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('PayPal certificate file is not readable');
            }

            throw new \RuntimeException('PayPal certificate file is not readable');
        }

        // open ssl
        if (!file_exists($openssl)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('openssl command not found');
            }

            throw new \RuntimeException('openssl command not found');
        }

        if (!is_executable($openssl)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('openssl is not executable');
            }

            throw new \RuntimeException('openssl is not executable');
        }
    }

    /**
     * Encrypt paypal information using openssl with a buffer.
     *
     *
     * @param $hash
     *
     * @throws \RuntimeException
     *
     * @return string the encrypted data
     */
    public function encryptViaBuffer($hash)
    {
        $this->checkPaypalFiles();

        $key_file = $this->getOption('key_file');
        $cert_file = $this->getOption('cert_file');
        $paypal_cert_file = $this->getOption('paypal_cert_file');
        $openssl = $this->getOption('openssl');

        $openssl_cmd = "$openssl smime -sign -signer $cert_file -inkey $key_file ".
            "-outform der -nodetach -binary | $openssl smime -encrypt ".
            "-des3 -binary -outform pem $paypal_cert_file";

        if ($this->getLogger()) {
            $this->getLogger()->debug(sprintf('[BasePaypalPayment::encrypt] command line=%s', $openssl_cmd));
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
        ];

        $process = proc_open($openssl_cmd, $descriptors, $pipes);

        if (\is_resource($process)) {
            foreach ($hash as $key => $value) {
                if ('' !== $value) {
                    fwrite($pipes[0], "$key=$value\n");
                }
            }
            fflush($pipes[0]);
            fclose($pipes[0]);

            $output = '';
            while (!feof($pipes[1])) {
                $output .= fgets($pipes[1]);
            }

            fclose($pipes[1]);
            $return_value = proc_close($process);

            return $output;
        }

        if ($this->getLogger()) {
            $this->getLogger()->emergency("Encrypting paypal data failed, Command line encryption failed \n cmd=$openssl_cmd \n hash=".print_r($hash, 1));
        }

        throw new \RuntimeException('Encrypting paypal data failed');
    }

    /**
     * Encrypt paypal information using openssl with a temporary file.
     *
     *
     * @param $hash
     *
     * @throws \RuntimeException
     *
     * @return string the encrypted data
     */
    public function encryptViaFile($hash)
    {
        $this->checkPaypalFiles();

        $key_file = $this->getOption('key_file');
        $cert_file = $this->getOption('cert_file');
        $openssl = $this->getOption('openssl');
        $paypal_cert_file = $this->getOption('paypal_cert_file');

        // create tmp file
        $filename = tempnam(sys_get_temp_dir(), 'sonata_paypal_');
        $contents = '';
        foreach ($hash as $name => $value) {
            $contents .= $name.'='.$value."\n";
        }

        if (!@file_put_contents($filename, $contents)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency(sprintf('encryptViaFile, unable to create buffer file : %s', $filename));
            }

            throw new \RuntimeException(sprintf('unable to create buffer file : %s', $filename));
        }

        $openssl_cmd = "$openssl smime -sign -signer $cert_file -inkey $key_file -outform der -nodetach -binary ".
            " < $filename ".
            " | $openssl smime -encrypt ".
            "-des3 -binary -outform pem $paypal_cert_file";

        if ($this->getLogger()) {
            $this->getLogger()->debug(sprintf('[BasePaypalPayment::encryptViaFile] command line=%s', $openssl_cmd));
        }

        $output = shell_exec($openssl_cmd);

        if (!@unlink($filename)) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency(sprintf('[BasePaypalPayment::encryptViaFile] unable to delete temporary file, %s', $filename));
            }
        }

        return $output;
    }

    /**
     * return the status list available from paypal system.
     *
     * @static
     *
     * @return array
     */
    public static function getPaymentStatusList()
    {
        return [
            self::PAYMENT_STATUS_CANCELED_REVERSAL => 'A reversal has been cancelled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you',
            self::PAYMENT_STATUS_COMPLETED => 'The payment has been completed, and the funds have been added successfully to your account balance',
            self::PAYMENT_STATUS_DENIED => 'You denied the payment. This happens only if the payment was previously pending because of possible reasons',
            self::PAYMENT_STATUS_FAILED => 'The payment has failed. This happens only if the payment was made from your customer’s bank account.',
            self::PAYMENT_STATUS_PENDING => 'The payment is pending. See pending_reason for more information.',
            self::PAYMENT_STATUS_REFUNDED => 'You refunded the payment.',
            self::PAYMENT_STATUS_REVERSED => 'A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element.',
            self::PAYMENT_STATUS_PROCESSED => 'A payment has been accepted.',
            self::PAYMENT_STATUS_VOIDED => 'This authorization has been voided.',
        ];
    }

    /**
     * @todo check if used
     *
     * @param unknown $webConnectorProvider
     */
    public function setWebConnectorProvider($webConnectorProvider): void
    {
        $this->webConnectorProvider = $webConnectorProvider;
    }

    /**
     * @todo check if used
     *
     * @return unknown $webConnectorProvider
     */
    public function getWebConnectorProvider()
    {
        return $this->webConnectorProvider;
    }
}
