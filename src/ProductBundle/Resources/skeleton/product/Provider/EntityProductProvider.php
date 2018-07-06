<?php
/*
 * This file is part of the sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sonata\ProductBundle\Provider;

use Sonata\ProductBundle\Model\BaseProductProvider;
use App\Sonata\ProductBundle\Controller\{{ product }}Controller;

/**
 * This file has been generated by the EasyExtends bundle ( https://sonata-project.org/easy-extends )
 *
 * References :
 *   custom repository : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en#querying:custom-repositories
 *   query builder     : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/query-builder/en
 *   dql               : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/dql-doctrine-query-language/en
 *
 * @author <yourname> <youremail>
 */
class {{ product }}ProductProvider extends BaseProductProvider
{
    public function getBaseControllerName()
    {
        return {{ product }}Controller::class;
    }

    public function getTemplatesPath(): string
    {
        return '@ApplicationSonataProduct/{{ product }}';
    }
}
