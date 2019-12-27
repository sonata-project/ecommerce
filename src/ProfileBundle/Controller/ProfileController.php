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

namespace Sonata\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This class is inspired from the FOS Profile Controller, except :
 *   - only twig is supported
 *   - separation of the user authentication form with the profile form.
 */
final class ProfileController extends Controller
{
    /**
     * @throws AccessDeniedException
     */
    public function dashboardAction(): Response
    {
        $user = $this->getUser();
        if (!\is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('SonataProfileBundle::dashboard.html.twig', [
            'user' => $user,
            'blocks' => $this->container->getParameter('sonata.profile.configuration.profile_blocks'),
        ]);
    }
}
