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

namespace Sonata\Component\Product;

use Doctrine\Persistence\ManagerRegistry;
use Sonata\Exporter\Source\DoctrineDBALConnectionSourceIterator;
use Sonata\Exporter\Source\SourceIteratorInterface;
use Sonata\Exporter\Source\SymfonySitemapSourceIterator;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class SeoProductIterator implements SourceIteratorInterface
{
    /**
     * @var SymfonySitemapSourceIterator
     */
    protected $iterator;

    /**
     * @param string $class
     * @param string $routeName
     */
    public function __construct(ManagerRegistry $registry, $class, RouterInterface $router, $routeName)
    {
        $tableName = $registry->getManager()->getClassMetadata($class)->table['name'];

        $dql = "SELECT p.id as productId, p.slug as slug,  p.updated_at as lastmod, 'weekly' as changefreq, '0.5' as priority "
            .'FROM '.$tableName.' p '
            .'WHERE p.enabled = 1';

        $source = new DoctrineDBALConnectionSourceIterator($registry->getConnection(), $dql);

        $this->iterator = new SymfonySitemapSourceIterator($source, $router, $routeName, ['productId' => null, 'slug' => null]);
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function rewind()
    {
        return $this->iterator->rewind();
    }
}
