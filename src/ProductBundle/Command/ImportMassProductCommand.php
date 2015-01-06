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

use Application\Sonata\ProductBundle\Entity\ProductCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\MediaBundle\Entity\MediaManager;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

class ImportMassProductCommand extends ContainerAwareCommand
{
    /**
     * @var ArrayCollection
     */
    protected $productManagers;

    /**
     * @var MediaManager
     */
    protected $mediaManager;

    /**
     * @var string
     */
    protected $productManagerKeyPattern;

    /**
     * @var string
     */
    protected $mediaProviderKey;

    /**
     * @var array
     */
    protected $setters;

    /**
     * @var string
     */
    protected $familyColumn;

    /**
     * @var int
     */
    protected $familyColumnIndex;

    /**
     * @var string
     */
    protected $skuColumn;

    /**
     * @var int
     */
    protected $skuColumnIndex;

    /**
     * @var string
     */
    protected $imageColumn;

    /**
     * @var int
     */
    protected $imageColumnIndex;

    /**
     * @var string
     */
    protected $categoryColumn;

    /**
     * @var int
     */
    protected $categoryColumnIndex;

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
                        'family-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product family column name',
                        'family'
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
                        'category-column',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Set the product category column name',
                        'category'
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
        $this->productManagers = new ArrayCollection();
        $this->mediaManager = $this->getContainer()->get('sonata.media.manager.media');
        $this->logger = $this->getContainer()->get('sonata.product.import.logger');
        $this->productManagerKeyPattern = $this->getContainer()->getParameter(
            'sonata.product.import.product_manager_key'
        );
        $this->mediaProviderKey = $this->getContainer()->getParameter(
            'sonata.product.import.media_provider_key'
        );
        $this->familyColumn = $input->getOption('family-column');
        $this->skuColumn = $input->getOption('sku-column');
        $this->imageColumn = $input->getOption('image-column');
        $this->categoryColumn = $input->getOption('category-column');
        $this->strict = $input->getOption('strict');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fp = $this->getFilePointer($input, $output);
        $index = 1;
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->beginTransaction();

        $startTime = microtime(true);
        try {
            while (!feof($fp)) {
                $data = fgetcsv(
                    $fp,
                    null,
                    $input->getOption('delimiter'),
                    $input->getOption('enclosure'),
                    $input->getOption('escape')
                );

                if ($index === 1) {
                    $this->checkColumnNameValidity($data);
                    $this->setters = array_map(
                        function ($fieldName) {
                            return Inflector::camelize($fieldName);
                        },
                        $data
                    );

                    $index++;
                    continue;
                }

                if (!is_array($data)) {
                    $index++;

                    continue;
                }

                $this->insertProduct($data, $index, $output);
                $index++;

                if (!($index % 100)) {
                    $this->commitTransaction($em, true);
                }
            }
        } catch (\Exception $e) {
            $em->rollback();
            $output->writeln("<comment>Transaction rolled back cause exception was thrown</comment>");

            throw $e;
        }

        $this->commitTransaction($em);
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
     * Check column names validity
     *
     * @param array $data
     */
    protected function checkColumnNameValidity(array $data)
    {
        $fields = array(
            'familyColumn'   => true,
            'skuColumn'      => true,
            'imageColumn'    => false,
            'categoryColumn' => false,
        );

        foreach ($fields as $field => $required) {
            if (!in_array($this->$field, $data) && $required) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to find column with name "%s". It is required to set product %s.',
                        $this->$field,
                        str_replace('Column', '', $field)
                    )
                );
            }

            $this->{$field.'Index'} = array_search($this->$field, $data);
        }
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
        $message = sprintf(
            ' > Starting index %d. Product %s with sku %s',
            $index,
            $data[$this->familyColumnIndex],
            $data[$this->skuColumnIndex]
        );

        try {
            $family = $data[$this->familyColumnIndex];
            /** @var ProductManagerInterface $productManager */
            $productManager = $this->getProductManager($family, $index);

            /** @var ProductInterface $product */
            $product = $productManager->findOneBy(array($this->skuColumn => $data[$this->skuColumnIndex]));
            $action = '<info>update</info>';

            if (!$product) {
                $product = $productManager->create();
                $action = '<info>create</info>';
            }

            if ($output->isVerbose()) {
                $output->writeLn(sprintf('%s - %s', $message, $action));
            }

            foreach ($this->setters as $pos => $name) {
                if ($pos !== $this->familyColumnIndex) {
                    switch (true) {
                        case $pos === $this->imageColumnIndex:
                            $value = $this->handleMedia($data[$pos], $product);
                            $product->setImage($value);
                            break;
                        case $pos === $this->categoryColumnIndex:
                            $value = $this->handleCategory($data[$pos], $product);
                            $product->setProductCategories($value);
                            break;
                        default:
                            $value = $data[$pos];
                            call_user_func(array($product, 'set'.ucfirst($name)), $value);
                            break;
                    }
                }
            }

            $productManager->save($product, false);
        } catch (\Exception $e) {
            $this->handleException($e, $index);
        }
    }

    /**
     * @param \Exception $e
     *
     * @throws \Exception
     */
    protected function handleException(\Exception $e)
    {
        if ($this->strict) {
            throw $e;
        }

        $this->addLog($e);
    }

    /**
     * @param \Exception $e
     */
    protected function addLog(\Exception $e)
    {
        $this->logger->error($e->getMessage());
    }

    /**
     * @param string $family
     * @param int    $index
     *
     * @return mixed|null|object
     *
     * @throws \RuntimeException
     */
    protected function getProductManager($family, $index)
    {
        if ($this->productManagers->containsKey($family)) {
            return $this->productManagers->get($family);
        }

        $managerKey = sprintf($this->productManagerKeyPattern, $family);
        $productManager = $this->getContainer()->get($managerKey);

        if (!$productManager) {
            throw new \RuntimeException(
                sprintf('Unable to find manager for %s with key %s. At file index %d', $family, $managerKey, $index)
            );
        }

        $this->productManagers->set($family, $productManager);

        return $productManager;
    }

    /**
     * @param string           $imagePath
     * @param ProductInterface $product
     *
     * @return MediaInterface
     */
    protected function handleMedia($imagePath, ProductInterface $product)
    {
        $mediaGetter = 'get'.ucfirst($this->imageColumn);
        $media = null;

        if (method_exists($product, $mediaGetter)) {
            $media = $product->$mediaGetter();
        }

        if (!$media instanceof MediaInterface) {
            $media = new Media();
        }

        $media->setName(basename($imagePath));
        $media->setBinaryContent($imagePath);
        $media->setEnabled(true);
        $media->setProviderName($this->mediaProviderKey);
        $media->setContext('sonata_product');
        $this->mediaManager->save($media);

        return $media;
    }

    /**
     * @param string $categorySlugs
     * @param ProductInterface $product
     *
     * @return ArrayCollection
     */
    protected function handleCategory($categorySlugs, ProductInterface $product)
    {
        $oldCategories = $product->getProductCategories();
        /** @var ProductCategoryManagerInterface $productCategoryManager */
        $productCategoryManager = $this->getContainer()->get('sonata.product_category.product');

        foreach ($oldCategories as $oldCategory) {
            $productCategoryManager->delete($oldCategory);
        }

        /** @var CategoryManagerInterface $categoryManager */
        $categoryManager = $this->getContainer()->get('sonata.classification.manager.category');
        $categoriesSlug = explode(',', $categorySlugs);
        $categories = new ArrayCollection();

        foreach ($categoriesSlug as $key => $slug) {
            /** @var CategoryInterface $category */
            if ($category = $categoryManager->findOneBy(array('slug' => trim($slug)))) {
                $productCategory = new ProductCategory();
                $productCategory->setCategory($category);
                $productCategory->setProduct($product);
                $productCategory->setEnabled(true);
                $productCategory->setMain($key == 0);
                $categories->add($productCategory);
            }
        }

        return $categories;
    }

    /**
     * Finalize import operation by flushing doctrine
     *
     * @param EntityManager $em
     * @param bool          $reOpen
     */
    protected function commitTransaction(EntityManager $em, $reOpen = false)
    {
        $em->commit();
        $em->flush();
        $em->getUnitOfWork()->clear();

        if ($reOpen) {
            $em->beginTransaction();
        }
    }
}
