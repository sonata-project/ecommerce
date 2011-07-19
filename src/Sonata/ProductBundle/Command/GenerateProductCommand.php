<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\Util\Filesystem;
use Sonata\Component\Generator\Mustache;

/**
 * Create a new Product
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GenerateProductCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('product', InputArgument::REQUIRED, 'The product to create'),
                new InputArgument('service_id', InputArgument::REQUIRED, 'The service id to define'),
            ))
            ->setName('sonata:product:generate')
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // find a better way to detect the Application folder
        $bundle_dir = sprintf("%s/../src/Application/Sonata/ProductBundle",
            $this->getContainer()->get('kernel')->getRootDir()
        );

        if (!is_dir($bundle_dir)) {
            throw new \Exception('Please initialize a ProductBundle first in the Application directory');
        }

        $output->writeln(sprintf('Generating files for the <info>%s</info>', $input->getArgument('product')));

        $output->writeln(' > mirroring skeleton files');

        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Resources/skeleton/product', $bundle_dir);

        $output->writeln(' > mustaching skeleton files');

        Mustache::renderDir($bundle_dir, array(
            'product' => $input->getArgument('product'),
        ));

        $renames = array(
            // entity
            '%s/Entity/Entity.php'                         => '%s/Entity/%s.php',
            '%s/Provider/EntityProductProvider.php'        => '%s/Provider/%sProductProvider.php',
            '%s/Resources/config/doctrine/Entity.orm.xml'  => '%s/Resources/config/doctrine/%s.orm.xml',

            // behavior
            '%s/Controller/Controller.php'                 => '%s/Controller/Controller.php',

            // templates
            '%s/Resources/views/Entity/view.html.twig'          => '%s/Resources/views/%s/view.hmtl.twig',
        );

        $dirs = array(
            sprintf('%s/Resources/views/%s', $bundle_dir, $input->getArgument('product')),
            sprintf('%s/Product/%s',         $bundle_dir, $input->getArgument('product')),
            sprintf('%s/Provider',           $bundle_dir),
        );

        foreach ($dirs as $dir ) {
            $filesystem->mkdir($dir);
        }

        $output->writeln(' > renaming skeleton files');

        foreach ($renames as $from => $to) {
            $from = sprintf($from, $bundle_dir);
            $to = sprintf($to, $bundle_dir, $input->getArgument('product'));

            if (is_file($to) || is_dir($to)) {
                $output->writeln(sprintf(' <info>-</info> deleting unused file : <comment>%s</comment>', basename($from)));
                $filesystem->remove($from);

                continue;
            }

            $output->writeln(sprintf(' <info>+</info> rename <comment>%s</comment> to <comment>%s</comment>', $from, $to));

            $filesystem->rename($from, $to);
        }

        $filesystem->remove(array(
            sprintf('%s/Product/Entity', $bundle_dir),
            sprintf('%s/Resources/views/Entity', $bundle_dir),
        ));

        $product = $input->getArgument('product');
        $service = $input->getArgument('service_id');

        $output->write(Mustache::renderString(<<<CONFIG

<info>1. Add this service definition</info>

<comment>services:</comment>
    {{ service }}.manager:
        class: Sonata\ProductBundle\Entity\ProductManager
        arguments:
            - Application\Sonata\ProductBundle\Entity\{{ product }}
            - @sonata.product.entity_manager

    {{ service }}.type:
        class: Application\Sonata\ProductBundle\Provider\{{ product }}ProductProvider

<info>2. Add this service configuration</info>

<comment>sonata_product:</comment>
    products:
        {{ service }}:
            provider: {{ service }}.type
            manager: {{ service }}.manager


<info>3. Tweak the product to match its functional requirements</info>

                                <comment>Good luck !</comment>


CONFIG
, array('service' => $service, 'product' => $product)
));
    }
}
