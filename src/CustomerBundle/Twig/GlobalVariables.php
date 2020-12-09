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

namespace Sonata\CustomerBundle\Twig;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class GlobalVariables
{
    /**
     * @var string
     */
    private $profileTemplate;

    public function __construct(string $profileTemplate)
    {
        $this->profileTemplate = $profileTemplate;
    }

    public function getProfileTemplate(): string
    {
        return $this->profileTemplate;
    }
}
