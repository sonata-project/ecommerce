<?php
/*
 * This file is part of the Sonata Connector project.
 *
 * (c) Sylvain Rascar <sylvain.rascar@fullsix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Command;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Psr\Log\InvalidArgumentException;

/**
 * Return useful data on the database schema
 *
 * Class SonataDoctrineUtils
 * @package Sonata\ProductBundle\Command
 */
class SonataDoctrineUtilsCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected $defaultDumpDirectory;

    /**
     * @var string
     */
    protected $baseProductClass;

    /**
     * @var string
     */
    protected $baseMediaClass;

    /**
     * @var string
     */
    protected $baseProductCategoryClass;

    /**
     * @var string
     */
    protected $baseCategoryClass;

    /**
     * @var array $allowedActions
     */
    protected $allowedActions = [
        'dump-meta' => null,
        'dump-products-meta' => 'baseProductClass',
        'dump-media-meta' => 'baseMediaClass',
        'dump-product-category-meta' => 'baseProductCategoryClass',
        'dump-category-meta' => 'baseCategoryClass',
    ];

    /**
     * @var array $metadata
     */
    protected $metadata;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('sonata:doctrine:utils')
            ->setDefinition(
                [
                    new InputArgument(
                        'action', InputArgument::REQUIRED, sprintf(
                            'The action to execute [%s]',
                            implode(' | ', array_keys($this->allowedActions))
                        )
                    ),
                    new InputOption(
                        'filename', 'f', InputOption::VALUE_OPTIONAL,
                        'If filename is specified, result will be dump into this file under json format.', null
                    ),
                ]
            )
            ->setDescription(
                'Get information on the current Doctrine\'s schema'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->defaultDumpDirectory = $this->getContainer()->getParameter('sonata.doctrine.utils.dump_directory');
        $this->baseProductClass = $this->getContainer()->getParameter('sonata.doctrine.utils.base_product_class');
        $this->baseMediaClass = $this->getContainer()->getParameter('sonata.doctrine.utils.base_media_class');
        $this->baseProductCategoryClass = $this->getContainer()->getParameter('sonata.doctrine.utils.base_product_category_class');
        $this->baseCategoryClass = $this->getContainer()->getParameter('sonata.doctrine.utils.base_category_class');

        $output->writeln(sprintf('Initialising Doctrine metadata.'));
        $manager = $this->getContainer()->get('doctrine')->getManager();
        $metadata = $manager->getMetadataFactory()->getAllMetadata();

        $allowedMeta = $this->filterMetadata($metadata, $input, $output);

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach ($allowedMeta as $meta) {
            $this->metadata[$meta->getName()] = $this->normalizeMeta($meta);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!array_key_exists($input->getArgument('action'), $this->allowedActions)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid argument %s. Allowed arguments are [%s].',
                    $input->getArgument('action'),
                    implode(' | ', array_keys($this->allowedActions))
                )
            );
        }

        if (!$this->metadata) {
            $output->writeln(sprintf('<error>No meta was found</error>'));

            return 1;
        }

        $this->dumpMetadata($this->metadata, $input, $output);

        return 0;
    }

    /**
     * Display the list of entities handled by Doctrine and their fields
     *
     * @param array           $metadata
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function dumpMetadata(array $metadata, InputInterface $input, OutputInterface $output)
    {
        foreach ($metadata as $name => $meta) {
            $output->writeln(sprintf('<info>%s</info>', $name));

            foreach ($meta['fields'] as $fieldName => $columnName) {
                $output->writeln(sprintf('  <comment>></comment> %s <info>=></info> %s', $fieldName, $columnName));
            }
        }
        $output->writeln('---------------');
        $output->writeln('----  END  ----');
        $output->writeln('---------------');
        $output->writeln('');

        if ($input->getOption('filename')) {
            $directory = dirname($input->getOption('filename'));
            $filename = basename($input->getOption('filename'));

            if (empty($directory) || $directory == '.') {
                $directory = $this->defaultDumpDirectory;
            }

            $adapter = new LocalAdapter($directory, true);
            $fileSystem = new Filesystem($adapter);
            $success = $fileSystem->write($filename, json_encode($metadata), true);

            if ($success) {
                $output->writeLn(sprintf('<info>File %s/%s successfully created</info>', $directory, $filename));
            } else {
                $output->writeLn(sprintf('<error>File %s/%s could not be created</error>', $directory, $filename));
            }
        }
    }

    /**
     * @param array           $metadata
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    private function filterMetadata(array $metadata, InputInterface $input, OutputInterface $output)
    {
        $allowedMeta = [];
        $action = $input->getArgument('action');

        switch ($action) {
            case 'dump-meta':
                $allowedMeta = $metadata;
                break;
            default :
                if (array_key_exists($action, $this->allowedActions)) {
                    $allowedMeta = array_filter(
                        $metadata,
                        function ($meta) use ($action) {
                            /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
                            return $meta->rootEntityName === $this->{$this->allowedActions[$action]};
                        }
                    );
                }

                break;
        }

        return $allowedMeta;
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata $meta
     *
     * @return array
     */
    private function normalizeMeta($meta)
    {
        $normalizedMeta = [];
        $fieldMappings = $meta->fieldMappings;

        $normalizedMeta['table'] = $meta->table['name'];

        foreach ($fieldMappings as $field) {
            $normalizedMeta['fields'][$field['fieldName']] = isset($field['columnName']) ? $field['columnName'] : null;
        }

        return $normalizedMeta;
    }
}
