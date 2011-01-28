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

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

/**
 * Create a new Product
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GenerateProductCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('product', InputArgument::REQUIRED, 'The product to create'),
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
        $bundle_dir = sprintf("%s/../src/Application/ProductBundle",
            $this->container->getKernelService()->getRootDir()
        );

        if(!is_dir($bundle_dir)) {
            throw new Exception('Please initialize a ProductBundle first in the Application directory');
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
            '%s/Entity/Entity.php'                            => '%s/Entity/%s.php',
            '%s/Entity/EntityRepository.php'                  => '%s/Entity/%sRepository.php',
            '%s/Resources/config/doctrine/metadata/orm/Application.ProductBundle.Entity.Entity.dcm.xml' => '%s/Resources/config/doctrine/metadata/orm/Application.ProductBundle.Entity.%s.dcm.xml',

            // behavior
            '%s/Controller/Controller.php'                    => '%s/Controller/Controller.php',
            '%s/Product/Entity/EntityAddBasket.php'           => '%1$s/Product/%2$s/%2$sAddBasket.php',
            '%s/Product/Entity/EntityAddBasketForm.php'       => '%1$s/Product/%2$s/%2$sAddBasketForm.php',

            // templates
            '%s/Resources/views/Entity/view.twig'             => '%s/Resources/views/%s/view.twig',
        );

        $dirs = array(
            sprintf('%s/Resources/views/%s', $bundle_dir, $input->getArgument('product')),
            sprintf('%s/Product/%s',         $bundle_dir, $input->getArgument('product')),
        );
        
        foreach($dirs as $dir ) {
            $filesystem->mkdirs($dir);
        }

        $output->writeln(' > renaming skeleton files');

        
        foreach($renames as $from => $to) {
            $from = sprintf($from, $bundle_dir);
            $to = sprintf($to, $bundle_dir, $input->getArgument('product'));
            
            if(is_file($to) || is_dir($to)) {

//                $output->writeln(sprintf(' <info>-</info> deleting unused file : <comment>%s</comment>', basename($from)));
                $filesystem->remove($from);

                continue;
            }

            $output->writeln(sprintf(' <info>+</info> rename <comment>%s</comment> to <comment>%s</comment>', $from, $to));
            
            $filesystem->copy($from, $to);

        }

        $filesystem->remove(array(
            sprintf('%s/Product/Entity', $bundle_dir),
            sprintf('%s/Resources/views/Entity', $bundle_dir),
        ));

        $product = $input->getArgument('product');
        $product_code = strtolower($product);
        
        $output->write(<<<CONFIG

<info>1. Add this configuration settings into your config.yml file</info>

sonata_product.config:
    products:
        - { id: $product_code, name: $product, enabled: true, class: Application\Sonata\ProductBundle\Entity\\$product }

<info>2. Tweak the product to match its functional requirements</info>

                                <comment>Good luck !</comment>


CONFIG
        );
    }
}
