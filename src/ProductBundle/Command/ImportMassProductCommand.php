<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Rascar Sylvain <sylvain.rascar@fullsix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Command;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Sonata\ProductBundle\Import\ImportProductService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportMassProductCommand extends ContainerAwareCommand
{
    /**
     * @var ImportProductService
     */
    protected $importProductService;

    /**
     * @var array
     */
    protected $setters;

    /**
     * @var array
     */
    protected $fieldMapping;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('sonata:product:add-multiple')
            ->setDescription('Add products in mass into the database')
            ->setDefinition(
                array(
                    new InputOption('file', null, InputOption::VALUE_OPTIONAL, 'The file to parse'),
                    new InputOption(
                        'delimiter',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the field delimiter (one character only)',
                        ','
                    ),
                    new InputOption(
                        'enclosure',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the field enclosure character (one character only).',
                        '"'
                    ),
                    new InputOption(
                        'escape',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the escape character (one character only). Defaults as a backslash',
                        '\\'
                    ),
                    new InputOption(
                        'type-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product type column name',
                        'type'
                    ),
                    new InputOption(
                        'sku-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product sku column name',
                        'sku'
                    ),
                    new InputOption(
                        'image-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product image column name',
                        'image'
                    ),
                    new InputOption(
                        'categories-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product category column name',
                        'categories'
                    ),
                    new InputOption(
                        'strict',
                        null,
                        InputOption::VALUE_NONE,
                        'If strict is true, process will stop on exception. Otherwise, it will try to process the next line'
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->importProductService = $this->getContainer()->get('sonata.product.import.service');
        $this->logger = $this->getContainer()->get('sonata.product.import.logger');
        $this->strict = $input->getOption('strict');
        $this->fieldMapping['type'] = $input->getOption('type-column');
        $this->fieldMapping['sku'] = $input->getOption('sku-column');
        $this->fieldMapping['image'] = $input->getOption('image-column');
        $this->fieldMapping['categories'] = $input->getOption('categories-column');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fp = $this->getFilePointer($input, $output);
        $index = 0;
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        // disable logger to prevent memory leak
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $em->beginTransaction();
        $startTime = microtime(true);

        while (!feof($fp)) {
            $index++;
            $data = fgetcsv(
                $fp,
                null,
                $input->getOption('delimiter'),
                $input->getOption('enclosure'),
                $input->getOption('escape')
            );

            if ($index === 1) {
                if (!is_array($data)) {
                    throw new \InvalidArgumentException('Unable to parse column names');
                }

                $this->importProductService->setMapping($this->fieldMapping);
                $this->setters = $data;

                continue;
            }

            if (!is_array($data)) {
                continue;
            }

            try {
                $this->insertProduct($data, $index, $output);

                if (!($index % 100)) {
                    $this->commitTransaction(true);
                }
            } catch (\Exception $e) {
                $em->rollback();
                $output->writeln("<comment>Transaction rolled back cause exception was thrown</comment>");
                $this->handleException($e);
            }
        }

        $this->commitTransaction();
        $endTime = microtime(true);
        $output->writeln(sprintf('Process time: %d secs', ($endTime - $startTime)));
        $output->writeln("Done!");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return resource
     */
    protected function getFilePointer(InputInterface $input, OutputInterface $output)
    {
        if (ftell(STDIN) !== false) {
            return STDIN;
        }

        if (!$input->getOption('file')) {
            throw new \RuntimeException('Please provide a CSV file argument or CSV input stream');
        }

        return fopen($input->getOption('file'), 'r');
    }

    /**
     * Insert or update a product according to the given data
     *
     * @param array           $data
     * @param int             $index
     * @param OutputInterface $output
     */
    protected function insertProduct(array $data, $index, OutputInterface $output)
    {
        $formattedData = array_combine($this->setters, $data);
        $status = $this->importProductService->importProduct($formattedData, false);

        if ($output->isVerbose()) {
            $message = sprintf(
                ' > Starting index %d. Product %s with sku %s',
                $index,
                $formattedData[$this->fieldMapping['type']],
                $formattedData[$this->fieldMapping['sku']]
            );
            $action = $status === ImportProductService::UPDATE_STATUS ? '<info>update</info>' : '<info>create</info>';
            $output->writeLn(sprintf('%s - %s', $message, $action));
        }
    }

    /**
     * @param \Exception $e
     *
     * @throws \Exception
     */
    protected function handleException(\Exception $e)
    {
        $this->addLog($e);

        if ($this->strict) {
            throw $e;
        }
    }

    /**
     * @param \Exception $e
     */
    protected function addLog(\Exception $e)
    {
        $this->logger->error($e->getMessage(), array('exception' => $e));
    }

    /**
     * Finalize import operation by flushing doctrine
     *
     * @param bool $reOpen
     */
    protected function commitTransaction($reOpen = false)
    {
        $em = $this->getEntityManager();
        $em->commit();
        $em->flush();
        $em->getUnitOfWork()->clear();
        $em->clear();

        if ($reOpen) {
            $em->beginTransaction();
        }
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!$em->isOpen()) {
            $this->getContainer()->get('doctrine')->resetEntityManager();
            $em = $this->getContainer()->get('doctrine')->getManager();
        }

        return $em;
    }
}
