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

namespace Sonata\ProductBundle\Command;

use Sonata\Component\Generator\Mustache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Create a new Product.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GenerateProductCommand extends Command
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel, ?string $name = null)
    {
        $this->kernel = $kernel;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('product', InputArgument::REQUIRED, 'The product to create'),
                new InputArgument('service_id', InputArgument::REQUIRED, 'The service id to define'),
                new InputArgument('namespace_prefix', InputArgument::OPTIONAL, 'The namespace prefix for the classes'),
            ])
            ->setName('sonata:product:generate')
            ->setDescription('Generates required files for a new Product');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // find a better way to detect the Application folder
        $bundle_dir = sprintf(
            '%s/src/Sonata/ProductBundle',
            $this->kernel->getProjectDir()
        );

        if (!is_dir($bundle_dir)) {
            throw new \Exception('Please initialize a ProductBundle first in the source directory');
        }

        $output->writeln(sprintf('Generating files for the <info>%s</info>', $input->getArgument('product')));

        $output->writeln(' > mirroring skeleton files');

        $bundle = $this->kernel->getBundle('SonataProductBundle', false);

        $vendorPath = $bundle->getPath();

        $filesystem = new Filesystem();
        $filesystem->mirror($vendorPath.'/Resources/skeleton/product', $bundle_dir);

        $output->writeln(' > mustaching skeleton files');

        $product = ucfirst($input->getArgument('product'));
        $namespacePrefix = ucfirst($input->getArgument('namespace_prefix')) ?? '';

        Mustache::renderDir($bundle_dir, [
            'product' => $product,
            'namespace_prefix' => $namespacePrefix,
            'root_name' => strtolower(preg_replace('/[A-Z]/', '_\\0', $product)),
        ]);

        $renames = [
            // entity
            '%s/Entity/Entity.php' => '%s/Entity/%s.php',
            '%s/Repository/Repository.php' => '%s/Repository/%sRepository.php',
            '%s/Provider/EntityProductProvider.php' => '%s/Provider/%sProductProvider.php',
            '%s/Resources/config/doctrine/Entity.orm.xml' => '%s/Resources/config/doctrine/%s.orm.xml',
            '%s/Resources/config/serializer/Entity.xml' => '%s/Resources/config/serializer/Entity.%s.xml',

            // controller
            '%s/Controller/Controller.php' => '%s/Controller/%sController.php',

            // templates
            '%s/Resources/views/Entity/view.html.twig' => '%s/Resources/views/%s/view.html.twig',
            '%s/Resources/views/Entity/form_basket_element.html.twig' => '%s/Resources/views/%s/form_basket_element.html.twig',
            '%s/Resources/views/Entity/final_review_basket_element.html.twig' => '%s/Resources/views/%s/final_review_basket_element.html.twig',
        ];

        $dirs = [
            sprintf('%s/Resources/views/%s', $bundle_dir, $product),
            sprintf('%s/Product/%s', $bundle_dir, $product),
            sprintf('%s/Provider', $bundle_dir),
        ];

        foreach ($dirs as $dir) {
            $filesystem->mkdir($dir);
        }

        $output->writeln(' > renaming skeleton files');

        foreach ($renames as $from => $to) {
            $from = sprintf($from, $bundle_dir);
            $to = sprintf($to, $bundle_dir, $product);

            if (is_file($to) || is_dir($to)) {
                $output->writeln(sprintf(' <info>-</info> deleting unused file : <comment>%s</comment>', basename($from)));
                $filesystem->remove($from);

                continue;
            }

            $output->writeln(sprintf(' <info>+</info> rename <comment>%s</comment> to <comment>%s</comment>', $from, $to));

            $filesystem->rename($from, $to);
        }

        $filesystem->remove([
            sprintf('%s/Product/Entity', $bundle_dir),
            sprintf('%s/Resources/views/Entity', $bundle_dir),
        ]);

        $service = $input->getArgument('service_id');

        $output->write(Mustache::renderString(
            <<<CONFIG

<info>1. Add this service definition</info>

<comment>services:</comment>
    {{ service }}.manager:
        class: Sonata\ProductBundle\Entity\ProductManager
        arguments:
            - App\Sonata\ProductBundle\Entity\{{ product }}
            - "@doctrine"

    {{ service }}.type:
        class: App\Sonata\ProductBundle\Provider\{{ product }}ProductProvider
        arguments:
            - "@jms_serializer"

<info>2. Add this service configuration</info>

<comment>sonata_product:</comment>
    products:
        {{ service }}:
            provider: {{ service }}.type
            manager: {{ service }}.manager

<info>3. Define the Product serialization inheritance :</info>

Add this line in <comment>/src/Sonata/ProductBundle/Resources/config/serializer/Entity.Product.xml</comment>

    <discriminator-class value="{{ service }}">App\Sonata\ProductBundle\Entity\{{ product }}</discriminator-class>

You can customize the serialization of your Product by editing /src/Sonata/ProductBundle/Resources/config/serializer/Entity.Product.xml
(see JMS serializer documentation for more information).

<info>4. Tweak the Product to match its functional requirements</info>

                                <comment>Good luck !</comment>


CONFIG
            ,
            ['service' => $service, 'product' => $product]
        ));
    }
}
