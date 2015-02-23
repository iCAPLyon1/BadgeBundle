<?php

namespace Icap\BadgeBundle\Listener;

use Icap\BadgeBundle\Entity\Badge;
use Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler;
use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @DI\Service("icap_badge.entity_listener.badge")
 * @DI\Tag("doctrine.entity_listener")
 */
class LocaleSetterListener
{
    /** @var \Claroline\CoreBundle\Library\Configuration\PlatformConfigurationHandler */
    private $platformConfigHandler;

    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /**
     * @DI\InjectParams({
     *     "platformConfigHandler" = @DI\Inject("claroline.config.platform_config_handler"),
     *     "securityContext"       = @DI\Inject("security.context")
     * })
     */
    public function __construct(PlatformConfigurationHandler $platformConfigHandler, SecurityContext $securityContext)
    {
        $this->platformConfigHandler = $platformConfigHandler;
        $this->securityContext       = $securityContext;
    }

    /**
     * Sets the locale on a badge.
     *
     * @param Badge              $badge
     * @param LifecycleEventArgs $event
     */
    public function postLoad(Badge $badge, LifecycleEventArgs $event)
    {
        $platformLocale = $this->platformConfigHandler->getParameter('locale_language');
        $userLocale = null;

        if ($token = $this->securityContext->getToken()) {
            if ('anon.' !== $user = $token->getUser()) {
                $userLocale = $user->getLocale();
            }
        }

        $badge->setLocale($userLocale ?: $platformLocale);
    }
}
