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

namespace Sonata\Component\Payment\Scellius;

use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\BasePayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ScelliusPayment extends BasePayment
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var ScelliusTransactionGeneratorInterface
     */
    protected $transactionGenerator;

    /**
     * @param RouterInterface                       $router
     * @param LoggerInterface                       $logger
     * @param EngineInterface                       $templating
     * @param ScelliusTransactionGeneratorInterface $transactionGenerator
     * @param bool                                  $debug
     */
    public function __construct(RouterInterface $router, LoggerInterface $logger, EngineInterface $templating, ScelliusTransactionGeneratorInterface $transactionGenerator, $debug)
    {
        $this->templating = $templating;
        $this->router = $router;
        $this->debug = $debug;
        $this->transactionGenerator = $transactionGenerator;

        $this->setLogger($logger);
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getCurrencyList()
    {
        return [
            'EUR' => ['name' => 'Euro',                  'code' => 978, 'fraction' => 2],
            'USD' => ['name' => 'Dollar Américain',      'code' => 840, 'fraction' => 2],
            'CHF' => ['name' => 'Franc Suisse',          'code' => 756, 'fraction' => 2],
            'GBP' => ['name' => 'Livre Sterling',        'code' => 826, 'fraction' => 2],
            'CAD' => ['name' => 'Dollar Canadien',       'code' => 124, 'fraction' => 2],
            'JPY' => ['name' => 'Yen',                   'code' => 392, 'fraction' => 0],
            'MXN' => ['name' => 'Peso Mexicain',         'code' => 484, 'fraction' => 2],
            'TRY' => ['name' => 'Nouvelle Livre Turque', 'code' => 949, 'fraction' => 2],
            'AUD' => ['name' => 'Dollar Australien',     'code' => 036, 'fraction' => 2],
            'NZD' => ['name' => 'Dollar Néo-Zélandais',  'code' => 554, 'fraction' => 2],
            'NOK' => ['name' => 'Couronne Norvégienne',  'code' => 578, 'fraction' => 2],
            'BRL' => ['name' => 'Real Brésilien',        'code' => 986, 'fraction' => 2],
            'ARS' => ['name' => 'Peso Argentin',         'code' => 032, 'fraction' => 2],
            'KHR' => ['name' => 'Riel',                  'code' => 116, 'fraction' => 2],
            'TWD' => ['name' => 'Dollar de Taiwan',      'code' => 901, 'fraction' => 2],
            'SEK' => ['name' => 'Couronne Suédoise',     'code' => 752, 'fraction' => 2],
            'DKK' => ['name' => 'Couronne Danoise',      'code' => 208, 'fraction' => 2],
            'KRW' => ['name' => 'Won',                   'code' => 410, 'fraction' => 0],
            'SGD' => ['name' => 'Dollar de Singapour',   'code' => 702, 'fraction' => 2],
            'XPF' => ['name' => 'Franc Polynésien',      'code' => 953, 'fraction' => 0],
            'XOF' => ['name' => 'Franc CFA',             'code' => 952, 'fraction' => 0],
        ];
    }

    /**
     * @return array
     */
    public static function getCVVFlags()
    {
        return [
            '0' => 'Le numéro de contrôle n’est pas remonté par le commerçant',
            '1' => 'Le numéro de contrôle est présent',
            '2' => 'Le numéro de contrôle est présent sur la carte  du porteur mais illisible (uniquement pour les cartes CB, VISA et MASTERCARD)',
            '9' => 'Le porteur a informé le commerçant que le numéro de contrôle n’était pas imprimé sur sa carte (uniquement pour les cartes CB, VISA, MASTERCARD et FINAREF)',
        ];
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getCVVResponseCode()
    {
        return [
            '4E' => 'Numéro de contrôle incorrect',
            '4D' => 'Numéro de contrôle correct',
            '50' => 'Numéro de contrôle non traité',
            '53' => 'Le numéro de contrôle est absent de la demande d’autorisation',
            '55' => 'La banque de l’internaute n’est pas certifiée, le contrôle n’a pu être effectué.',
            'NO' => 'Pas de cryptogramme sur la carte.',
            '' => 'Pour les cartes AMEX, American Express ne retourne pas de code réponse spécifique à la vérification du numéro de contrôle.'.
                    'Si le code sécurité de la carte est faux, American Express retourne un code 05 dans le champ RESPONSE_CODE.'.
                    'Pour les cartes FINAREF, Finaref ne retourne pas de code réponse spécifique à la vérification du numéro de contrôle. ',
        ];
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getBankCodeResponses()
    {
        $responses = [];

        $responses[] = [
            'banks' => ['CB', 'VISA', 'MASTERCARD', 'AMEX'],
            'messages' => [
                ['00' => 'Transaction approuvée ou traitée avec succès'],
                ['02' => 'Contacter l\'émetteur de carte'],
                ['03' => 'Accepteur invalide'],
                ['04' => 'Conserver la carte'],
                ['05' => 'Ne pas honorer'],
                ['07' => 'Conserver la carte, conditions spéciales'],
                ['08' => 'Approuver après identification'],
                ['12' => 'Transaction invalide'],
                ['13' => 'Montant invalide'],
                ['14' => 'Numéro de porteur invalide'],
                ['15' => 'Emetteur de carte inconnu'],
                ['30' => 'Erreur de format'],
                ['31' => 'Identifiant de l\'organisme acquéreur inconnu'],
                ['33' => 'Date de validité de la carte dépassée'],
                ['34' => 'Suspicion de fraude'],
                ['41' => 'Carte perdue'],
                ['43' => 'Carte volée'],
                ['51' => 'Provision insuffisante ou crédit dépassé'],
                ['54' => 'Date de validité de la carte dépassée'],
                ['56' => 'Carte absente du fichier'],
                ['57' => 'Transaction non permise à ce porteur'],
                ['58' => 'Transaction interdite au terminal'],
                ['59' => 'Suspicion de fraude'],
                ['60' => 'L\'accepteur de carte doit contacter l\'acquéreur'],
                ['61' => 'Dépasse la limite du montant de retrait'],
                ['63' => 'Règles de sécurité non respectées'],
                ['68' => 'Réponse non parvenue ou reçue trop tard'],
                ['90' => 'Arrêt momentané du système'],
                ['91' => 'Emetteur de cartes inaccessible'],
                ['96' => 'Mauvais fonctionnement du système'],
                ['97' => 'Échéance de la temporisation de surveillance globale'],
                ['98' => 'Serveur indisponible routage réseau demandé à nouveau'],
                ['99' => 'Incident domaine initiateur'],
            ],
        ];

        $responses[] = [
            'banks' => ['AMEX'],
            'messages' => [
                '00' => 'Transaction approuvée ou traitée avec succès',
                '02' => 'Dépassement de plafond',
                '04' => 'Conserver la carte',
                '05' => 'Ne pas honorer',
                '97' => 'Échéance de la temporisation de surveillance globale',
            ],
        ];

        $responses[] = [
            'banks' => ['FINAREF'],
            'messages' => [
                '00' => 'Transaction approuvée',
                '03' => 'Commerçant inconnu - Identifiant de commerçant incorrect',
                '05' => 'Compte / Porteur avec statut bloqué ou invalide',
                '11' => 'Compte / porteur inconnu',
                '16' => 'Provision insuffisante',
                '20' => 'Commerçant invalide / Code monnaie incorrect /Opération commerciale inconnue / Opération commerciale invalide',
                '80' => 'Transaction approuvée avec dépassement',
                '81' => 'Transaction approuvée avec augmentation capital',
                '82' => 'Transaction approuvée NPAI',
                '83' => 'Compte / porteur invalide',
            ],
        ];

        return $responses;
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getLanguageCodes()
    {
        return [
            'fr' => 'Français',
            'ge' => 'Allemand',
            'en' => 'Anglais',
            'es' => 'Espagnol',
            'it' => 'Italien',
        ];
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getPaymentMeans()
    {
        return [
            'CB' => ['name' => 'Carte Bleue',           'network' => 'CB Nationale'],
            'VISA' => ['name' => 'VISA',                  'network' => 'CB Internationale'],
            'MASTERCARD' => ['name' => 'MASTERCARD',            'network' => 'CB Internationale'],
            'AMEX' => ['name' => 'AMEX',                  'network' => 'American Express'],
            'DINERS' => ['name' => 'DINER\'S CLUB',         'network' => 'DINERS'],
            'FINAREF' => ['name' => 'FINAREF',               'network' => 'FINAREF'],
            'FNAC' => ['name' => 'FNAC',                  'network' => 'FINAREF'],
            'CYRILLUS' => ['name' => 'CYRILLUS',              'network' => 'FINAREF'],
            'PRINTEMPS' => ['name' => 'PRINTEMPS',             'network' => 'FINAREF'],
            'KANGOUROU' => ['name' => 'KANGOUROU',             'network' => 'FINAREF'],
            'SURCOUF' => ['name' => 'SURCOUF',               'network' => 'FINAREF'],
            'POCKETCARD' => ['name' => 'POCKETCARD (Belgique)', 'network' => 'FINAREF'],
            'CONFORAMA' => ['name' => 'CONFORAMA',             'network' => 'FINAREF'],
            'NUITEA' => ['name' => 'NUITEA',                'network' => 'CETELEM'],
            'AURORE' => ['name' => 'AURORE',                'network' => 'CETELEM'],
            'PASS' => ['name' => 'PASS',                  'network' => 'CETELEM'],
            'PLURIEL' => ['name' => 'PLURIEL',               'network' => 'FRANFINANCE'],
            'TOYSRUS' => ['name' => 'TOYSRUS',               'network' => 'FRANFINANCE'],
            'CONNEXION' => ['name' => 'CONNEXION',             'network' => 'FRANFINANCE'],
            'HYPERMEDIA' => ['name' => 'HYPERMEDIA',            'network' => 'FRANFINANCE'],
            'DELATOUR' => ['name' => 'DELATOUR',              'network' => 'FRANFINANCE'],
            'NORAUTO' => ['name' => 'NORAUTO',               'network' => 'FRANFINANCE'],
            'NOUVFRONT' => ['name' => 'NOUVELLES FRONTIERES',  'network' => 'FRANFINANCE'],
            'SERAP' => ['name' => 'SERAP',                 'network' => 'FRANFINANCE'],
            'BOURBON' => ['name' => 'BOURBON',               'network' => 'FRANFINANCE'],
            'COFINOGA' => ['name' => 'COFINOGA',              'network' => 'COFINOGA'],
            'COFINOGA _BHV' => ['name' => 'BHV',                   'network' => 'COFINOGA'],
            'COFINOGA _CASINOGEANT' => ['name' => 'CASINOGEANT',           'network' => 'COFINOGA'],
            'COFINOGA _DIAC' => ['name' => 'DIAC',                  'network' => 'COFINOGA'],
            'COFINOGA _GL' => ['name' => 'GL',                    'network' => 'COFINOGA'],
            'COFINOGA _GOSPORT' => ['name' => 'GOSPORT',               'network' => 'COFINOGA'],
            'COFINOGA _MONOPRIX' => ['name' => 'MONOPRIX',              'network' => 'COFINOGA'],
            'COFINOGA _MRBRICOLAGE' => ['name' => 'MRBRICOLAGE',           'network' => 'COFINOGA'],
            'COFINOGA _SOFICARTE' => ['name' => 'SOFICARTE',             'network' => 'COFINOGA'],
            'COFINOGA _SYGMA' => ['name' => 'SYGMA',                 'network' => 'COFINOGA'],
            'JCB' => ['name' => 'JCB',                   'network' => 'JCB - Japanese Credit Bureau'],
            'DELTA' => ['name' => 'DELTA',                 'network' => 'NATWEST - GB'],
            'SWITCH' => ['name' => 'SWITCH',                'network' => 'NATWEST - GB'],
            'SOLO' => ['name' => 'SOLO',                  'network' => 'NATWEST - GB'],
        ];
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getResponseCode()
    {
        return [
            '00' => 'Autorisation acceptée',
            '02' => 'demande d’autorisation par téléphone à la banque à cause d’un dépassement de plafond d’autorisation sur la carte (cf. annexe I)',
            '03' => 'Champ merchant_id invalide, vérifier la valeur renseignée dans la requête / Contrat de vente à distance inexistant, contacter votre banque.',
            '05' => 'Autorisation refusée',
            '12' => 'Transaction invalide, vérifier les paramètres transférés dans la requête.',
            '17' => 'Annulation de l’internaute',
            '30' => 'Erreur de format.',
            '34' => 'Suspicion de fraude',
            '75' => 'Nombre de tentatives de saisie du numéro de carte dépassé.',
            '90' => 'Service temporairement indisponible',
        ];
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return 1;
    }

    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        return true;
    }

    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    public function isRequestValid(TransactionInterface $transaction)
    {
        return $transaction->get('check') === $this->generateUrlCheck($transaction->getOrder());
    }

    public function handleError(TransactionInterface $transaction)
    {
        if ($transaction->getOrder()->isOpen()) {
            $transaction->getOrder()->setPaymentStatus($transaction->getStatusCode());
        }

        $this->report($transaction);

        return new Response('ko', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        $data = $this->getResponseData($transaction);

        $parameters = $transaction->getParameters();
        $parameters['DECODED_DATA'] = $data;

        $transaction->setParameters($parameters);

        if (-1 === $data['code']) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_ERROR_VALIDATION);

            $transaction->getOrder()->setStatus(OrderInterface::STATUS_ERROR);
            $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_ERROR_VALIDATION);

            return false;
        } elseif (0 !== $data['code']) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_UNKNOWN);

            return false;
        }

        // error
        if ('00' !== $data['response_code']) {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_ERROR_VALIDATION);

            $transaction->getOrder()->setValidatedAt(new \DateTime());
            $transaction->getOrder()->setStatus(OrderInterface::STATUS_VALIDATED);
            $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_ERROR_VALIDATION);

            return false;
        }

        $transaction->setState(TransactionInterface::STATE_OK);
        $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);

        $transaction->getOrder()->setValidatedAt(new \DateTime());
        $transaction->getOrder()->setStatus(OrderInterface::STATUS_VALIDATED);
        $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_VALIDATED);

        return new Response();
    }

    public function isCallbackValid(TransactionInterface $transaction)
    {
        if (!$transaction->getOrder()) {
            return false;
        }

        if ($transaction->get('check') === $this->generateUrlCheck($transaction->getOrder())) {
            return true;
        }

        $transaction->setState(TransactionInterface::STATE_KO);
        $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);
        $transaction->addInformation('The callback is not valid');

        return false;
    }

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     *
     * @return string
     */
    public function getLanguage(OrderInterface $order)
    {
        $language = substr($order->getLocale(), 0, 2);

        $languages = self::getLanguageCodes();

        if (!isset($languages[$language])) {
            return $this->getOption('language', 'en');
        }

        return $language;
    }

    public function sendbank(OrderInterface $order)
    {
        $params = [
            'bank' => $this->getCode(),
            'reference' => $order->getReference(),
            'check' => $this->generateUrlCheck($order),
        ];

        $cmdLineParameters = [
            // base configuration
            'merchant_id' => $this->getOption('merchant_id'),
            'merchant_country' => $this->getOption('merchant_country'),
            'pathfile' => $this->getOption('pathfile'),
            'language' => $this->getLanguage($order),
            'payment_means' => $this->getOption('payment_means'),
            'header_flag' => $this->getOption('header_flag'),
            'capture_day' => $this->getOption('capture_day'),
            'capture_mode' => $this->getOption('capture_mode'),
            'bgcolor' => $this->getOption('bgcolor'),
            'block_align' => $this->getOption('block_align'),
            'block_order' => $this->getOption('block_order'),
            'textcolor' => $this->getOption('textcolor'),
            'normal_return_logo' => $this->getOption('normal_return_logo'),
            'cancel_return_logo' => $this->getOption('cancel_return_logo'),
            'submit_logo' => $this->getOption('submit_logo'),
            'logo_id' => $this->getOption('logo_id'),
            'logo_id2' => $this->getOption('logo_id2'),
            'advert' => $this->getOption('advert'),
            'background_id' => $this->getOption('background_id'),
            'templatefile' => $this->getOption('templatefile'),

            // runtime parameters
            'amount' => $this->getAmount($order->getTotalInc(), $order->getCurrency()->getLabel()),
            'currency_code' => $this->getCurrencyCode($order->getCurrency()->getLabel()),
            'transaction_id' => $this->transactionGenerator->generate($order),
            'normal_return_url' => $this->router->generate($this->getOption('url_return_ok'), $params, UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_return_url' => $this->router->generate($this->getOption('url_return_ko'), $params, UrlGeneratorInterface::ABSOLUTE_URL),
            'automatic_response_url' => $this->router->generate($this->getOption('url_callback'), $params, UrlGeneratorInterface::ABSOLUTE_URL),
            'caddie' => 'mon_caddie',
            'customer_id' => $order->getCustomer()->getId(),
            'customer_email' => $order->getCustomer()->getEmail(),
            'customer_ip_address' => '',
            'data' => $this->getOption('data'),
            'return_context' => '',
            'target' => '',
            'order_id' => $order->getReference(),
        ];

        // clean parameters
        $cmdLineOptions = [];
        foreach ($cmdLineParameters as $option => $value) {
            $cmdLineOptions[] = sprintf('%s=%s', $option, $this->encodeString($value));
        }

        $cmd = sprintf('cd %s && %s %s', $this->getOption('base_folder'), $this->getOption('request_command'), implode(' ', $cmdLineOptions));

        $this->logger->debug(sprintf('Running command : %s', $cmd));

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Error %d when executing Scellius command: "%s".', $process->getExitCode(), trim($process->getErrorOutput())));
        }

        //sortie de la fonction : $result=!code!error!buffer!
        //    - code=0  : la fonction génère une page html contenue dans la variable buffer
        //    - code=-1 : La fonction retourne un message d'erreur dans la variable error
        $data = explode('!', $process->getOutput());

        if (5 !== \count($data)) {
            throw new \RuntimeException('Invalid data count');
        }

        if ('0' === $data[1]) {
            $scellius = [
                'valid' => true,
                'content' => $data[3],
            ];
        } else {
            $scellius = [
                'valid' => false,
                'content' => $data[2],
            ];
        }

        return $this->templating->renderResponse($this->getOption('template'), [
            'order' => $order,
            'scellius' => $scellius,
            'debug' => $this->debug,
            'parameters' => $cmdLineParameters,
        ]);
    }

    public function encodeString($string)
    {
        return escapeshellcmd((string) $string);
    }

    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * @param TransactionInterface $transaction
     */
    public function applyTransactionId(TransactionInterface $transaction): void
    {
        $transaction->setTransactionId('n/a');
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    private function getResponseData(TransactionInterface $transaction)
    {
        $cmd = sprintf('cd %s && %s pathfile=%s message=%s ',
            $this->getOption('base_folder'),
            $this->getOption('response_command'),
            $this->getOption('pathfile'),
            $this->encodeString($transaction->get('DATA'))
        );

        $this->logger->debug(sprintf('Response Command : %s', $cmd));

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Error %d when executing Scellius command: "%s".', $process->getExitCode(), trim($process->getErrorOutput())));
        }

        //Sortie de la fonction : !code!error!v1!v2!v3!...!v29
        //  - code = 0  : la fonction retourne les données de la transaction dans les variables v1, v2, ...
        //              : Ces variables sont décrites dans le GUIDE DU PROGRAMMEUR
        //  - code = -1 : La fonction retourne un message d'erreur dans la variable error
        $data = explode('!', $process->getOutput());

        if (33 !== \count($data)) {
            throw new \RuntimeException('Invalid data count');
        }

        return [
            'code' => (int) $data[1],
            'error' => $data[2],
            'merchant_id' => $data[3],
            'merchant_country' => $data[4],
            'amount' => $data[5],
            'transaction_id' => $data[6],
            'payment_means' => $data[7],
            'transmission_date' => $data[8],
            'payment_time' => $data[9],
            'payment_date' => $data[10],
            'response_code' => $data[11],
            'payment_certificate' => $data[12],
            'authorisation_id' => $data[13],
            'currency_code' => $data[14],
            'card_number' => $data[15],
            'cvv_flag' => $data[16],
            'cvv_response_code' => $data[17],
            'bank_response_code' => $data[18],
            'complementary_code' => $data[19],
            'complementary_info' => $data[20],
            'return_context' => $data[21],
            'caddie' => $data[22],
            'receipt_complement' => $data[23],
            'merchant_language' => $data[24],
            'language' => $data[25],
            'customer_id' => $data[26],
            'order_id' => $data[27],
            'customer_email' => $data[28],
            'customer_ip_address' => $data[29],
            'capture_day' => $data[30],
            'capture_mode' => $data[31],
            'data' => $data[32],
        ];
    }

    /**
     * @param $currency
     *
     * @return mixed
     */
    private function getCurrencyCode($currency)
    {
        $list = self::getCurrencyList();

        if (isset($list[$currency])) {
            return $list[$currency]['code'];
        }

        throw new \RuntimeException('Invalid exception provided');
    }

    /**
     * @param $amount
     * @param $currency
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getAmount($amount, $currency)
    {
        $list = self::getCurrencyList();

        if (!isset($list[$currency])) {
            throw new \RuntimeException('Invalid currency provided');
        }

        return (int) (100 * bcmul('1', (string) $amount, $list[$currency]['fraction']));
    }
}
