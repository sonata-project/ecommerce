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

namespace Sonata\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_payment');

        $node
            ->children()
                ->scalarNode('selector')->defaultValue('sonata.payment.selector.simple')->cannotBeEmpty()->end()
                ->scalarNode('generator')->defaultValue('sonata.payment.generator.mysql')->cannotBeEmpty()->end()
                ->arrayNode('transformers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addPaymentSection($node);
        $this->addModelSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addPaymentSection(ArrayNodeDefinition $node): void
    {
        $node
            ->validate()
            ->ifTrue(function ($v) {
                foreach ($v['methods'] as $methodCode => $service) {
                    if (null === $service || '' === $service) {
                        foreach ($v['services'] as $serviceConf) {
                            if ($methodCode === $serviceConf['code']) {
                                break 2;
                            }
                        }

                        return true;
                    }
                }

                return false;
            })
            ->thenInvalid('Custom payment methods require a service id. Provided payment methods need to be configured with their method code as key.')
            ->end()
            ->children()
                ->arrayNode('services')
                    ->children()
                        ->arrayNode('paypal')
                            ->children()
                                ->scalarNode('name')->defaultValue('Paypal')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('paypal')->cannotBeEmpty()->end()
                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('shop_secret_key')->cannotBeEmpty()->end()
                                        ->scalarNode('web_connector_name')->defaultValue('curl')->cannotBeEmpty()->end()

                                        ->scalarNode('account')->defaultValue('your_paypal_account@fake.com')->cannotBeEmpty()->end()
                                        ->scalarNode('cert_id')->defaultValue('fake')->cannotBeEmpty()->end()
                                        ->scalarNode('debug')->defaultValue(false)->cannotBeEmpty()->end()
                                        ->scalarNode('paypal_cert_file')->defaultValue('%kernel.root_dir%/paypal_cert_pem_sandbox.txt')->cannotBeEmpty()->end()
                                        ->scalarNode('url_action')->defaultValue('https://www.sandbox.paypal.com/cgi-bin/webscr')->cannotBeEmpty()->end()

                                        ->scalarNode('class_order')->defaultValue('Application\Sonata\OrderBundle\Entity\Order')->cannotBeEmpty()->end()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()

                                        ->scalarNode('method')->defaultValue('encryptViaBuffer')->cannotBeEmpty()->end() // encryptViaFile || encryptViaBuffer

                                        ->scalarNode('key_file')->defaultValue('%kernel.root_dir%/my-prvkey.pem')->cannotBeEmpty()->end()
                                        ->scalarNode('cert_file')->defaultValue('%kernel.root_dir%/my-pubcert.pem')->cannotBeEmpty()->end()

                                        ->scalarNode('openssl')->defaultValue('/opt/local/bin/openssl')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('debug')
                            ->children()
                                ->scalarNode('name')->defaultValue('Debug Payment')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('debug')->cannotBeEmpty()->end()
                                ->scalarNode('browser')->defaultValue('sonata.payment.browser.curl')->cannotBeEmpty()->end()
                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('pass')
                            ->children()
                                ->scalarNode('name')->defaultValue('Pass')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('pass')->cannotBeEmpty()->end()
                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->scalarNode('browser')->defaultValue('sonata.payment.browser.curl')->cannotBeEmpty()->end()
                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('shop_secret_key')->cannotBeEmpty()->end()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('check')
                            ->children()
                                ->scalarNode('name')->defaultValue('Check')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('check')->cannotBeEmpty()->end()
                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->scalarNode('browser')->defaultValue('sonata.payment.browser.curl')->cannotBeEmpty()->end()
                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('shop_secret_key')->cannotBeEmpty()->end()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('scellius')
                            ->children()
                                ->scalarNode('name')->defaultValue('Scellius')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('scellius')->cannotBeEmpty()->end()
                                ->scalarNode('generator')->defaultValue('sonata.payment.provider.scellius.none_generator')->end()

                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()

                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()

                                        ->scalarNode('template')->defaultValue('SonataPaymentBundle:Payment:scellius.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('shop_secret_key')->cannotBeEmpty()->end()
                                        ->scalarNode('request_command')->cannotBeEmpty()->end()
                                        ->scalarNode('response_command')->cannotBeEmpty()->end()
                                        ->scalarNode('merchant_id')->cannotBeEmpty()->end()
                                        ->scalarNode('merchant_country')->cannotBeEmpty()->end()
                                        ->scalarNode('pathfile')->cannotBeEmpty()->end()
                                        ->scalarNode('language')->cannotBeEmpty()->end()
                                        ->scalarNode('payment_means')->cannotBeEmpty()->end()
                                        ->scalarNode('base_folder')->cannotBeEmpty()->end()
                                        ->scalarNode('data')->defaultValue('')->end()

                                        ->scalarNode('header_flag')->defaultValue('no')->cannotBeEmpty()->end()
                                        ->scalarNode('capture_day')->defaultValue('')->end()
                                        ->scalarNode('capture_mode')->defaultValue('')->end()

                                        // layout
                                        ->scalarNode('bgcolor')->defaultValue('')->end()
                                        ->scalarNode('block_align')->defaultValue('')->end()
                                        ->scalarNode('block_order')->defaultValue('')->end()
                                        ->scalarNode('textcolor')->defaultValue('')->end()

                                        // Only available on pre production
                                        ->scalarNode('normal_return_logo')->defaultValue('')->end()
                                        ->scalarNode('cancel_return_logo')->defaultValue('')->end()
                                        ->scalarNode('submit_logo')->defaultValue('')->end()
                                        ->scalarNode('logo_id')->defaultValue('')->end()
                                        ->scalarNode('logo_id2')->defaultValue('')->end()
                                        ->scalarNode('advert')->defaultValue('')->end()
                                        ->scalarNode('background_id')->defaultValue('')->end()
                                        ->scalarNode('templatefile')->defaultValue('')->end()
                                    ->end()
                                ->end()
                            ->end()
                          ->end()

                        ->arrayNode('ogone')
                            ->children()
                                ->scalarNode('name')->defaultValue('Ogone')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('ogone')->cannotBeEmpty()->end()

                                ->arrayNode('transformers')
                                    ->children()
                                        ->scalarNode('basket')->defaultValue('sonata.payment.transformer.basket')->cannotBeEmpty()->end()
                                        ->scalarNode('order')->defaultValue('sonata.payment.transformer.order')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()

                                ->arrayNode('options')
                                    ->children()
                                        ->scalarNode('url_callback')->defaultValue('sonata_payment_callback')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ko')->defaultValue('sonata_payment_error')->cannotBeEmpty()->end()
                                        ->scalarNode('url_return_ok')->defaultValue('sonata_payment_confirmation')->cannotBeEmpty()->end()

                                        ->scalarNode('form_url')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('catalog_url')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('home_url')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('pspid')->isRequired()->cannotBeEmpty()->end()

                                        ->scalarNode('sha_key')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('sha-out_key')->isRequired()->cannotBeEmpty()->end()

                                        ->scalarNode('template')->defaultValue('SonataPaymentBundle:Payment:ogone.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('methods')
                    ->useAttributeAsKey('code')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('order')->defaultValue('Application\\Sonata\\OrderBundle\\Entity\\Order')->end()
                        ->scalarNode('transaction')->defaultValue('Application\\Sonata\\PaymentBundle\\Entity\\Transaction')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
