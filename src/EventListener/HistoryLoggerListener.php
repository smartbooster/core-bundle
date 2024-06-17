<?php

namespace Smart\CoreBundle\EventListener;

use Smart\CoreBundle\Entity\User\UserProfileInterface;
use Smart\CoreBundle\Logger\HistoryLogger;
use Smart\CoreBundle\Utils\RequestUtils;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Controller to init internal variables of the HistoryLogger
 *
 * @author Mathieu Ducrot <mathieu.ducrot@smartbooster.io>
 */
class HistoryLoggerListener implements EventSubscriberInterface
{
    private string $context;

    public function __construct(
        protected RequestStack $requestStack,
        protected TokenStorageInterface $tokenStorage,
        protected HistoryLogger $historyLogger,
        protected string $domain,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Set the context base on the domain
        $request = $this->requestStack->getCurrentRequest();
        $this->context = RequestUtils::getContextFromHost($request->getHost(), $this->domain);
        $this->historyLogger->setContext($this->context);

        // Set the origin based on the action controller
        if (is_array($event->getController())) {
            /** @var string $controllerClass */
            $controllerClass = get_class($event->getController()[0]);
            $controllerAction = $event->getController()[1];
        } else {
            $controllerClass = null;
            $controllerAction = null;
        }
        $isCrudController = str_ends_with($controllerClass, '\CRUDController');
        if ($isCrudController && $controllerAction === 'createAction') {
            $this->historyLogger->setOrigin('h.crt_f');
        } elseif ($isCrudController && $controllerAction === 'editAction') {
            $this->historyLogger->setOrigin('h.upd_f');
        } elseif ($isCrudController && $controllerAction === 'importAction') {
            $this->historyLogger->setOrigin('h.imp_f');
        } elseif ($isCrudController && $controllerAction === 'archiveAction') {
            $this->historyLogger->setOrigin('h.arc_a');
        } elseif ($controllerClass !== null && str_ends_with($controllerClass, '\SecurityController') && $controllerAction === 'profile') {
            $this->historyLogger->setOrigin('h.prf_f');
        }

        // Set the user
        $userToken = $this->tokenStorage->getToken();
        $user = $userToken?->getUser();
        if ($user instanceof UserProfileInterface) {
            $this->historyLogger->setUser((string) $user);
            $this->historyLogger->setUserProfile($user->getProfile());
        }
    }
}
