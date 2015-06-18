<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Exporter\Source\DoctrineDBALConnectionSourceIterator;
use Exporter\Source\SourceIteratorInterface;
use Exporter\Source\SymfonySitemapSourceIterator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SeoProductIterator.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class SeoProductIterator implements SourceIteratorInterface
{
    /**
     * @var SymfonySitemapSourceIterator
     */
    protected $iterator;

    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     * @param string            $class
     * @param RouterInterface   $router
     * @param string            $routeName
     */
    public function __construct(RegistryInterface $registry, $class, RouterInterface $router, $routeName)
    {
        $tableName = $registry->getManager()->getClassMetadata($class)->table['name'];

        $dql = "SELECT p.id as productId, p.slug as slug,  p.updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority "
            .'FROM '.$tableName.' p '
            .'WHERE p.enabled = 1';

        $source = new DoctrineDBALConnectionSourceIterator($registry->getConnection(), $dql);

        $this->iterator = new SymfonySitemapSourceIterator($source, $router, $routeName, array('productId' => null, 'slug' => null));
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return $this->iterator->rewind();
    }
}
