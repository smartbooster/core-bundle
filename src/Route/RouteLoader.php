<?php

namespace Smart\CoreBundle\Route;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function Symfony\Component\String\u;

final class RouteLoader extends Loader
{
    /**
     * Defines how the routes must be configured on parent project
     * To enable routes add the following code to /config/routes :
     * _smart_core:
     *     resource: .
     *     type:     smart_core
     *     host:     "admin.%domain%"
     *
     * Note that the smart_core type ensures that the support will not overlap the type of the Loader for other bundle
     */
    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'smart_core' === $type;
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $collection = new RouteCollection();
        $routeNamePrefix = 'smart_core_monitoring_';
        $routePathPrefix = '/smart-core/monitoring/';
        $controller = 'Smart\CoreBundle\Controller\MonitoringController::';
        $collection->add(
            $routeNamePrefix . 'uptime',
            // MDT the path ^/anonymous must have PUBLIC_ACCESS in the project security.yaml config for the probe route to be accessible without user
            new Route('/anonymous' . $routePathPrefix . 'uptime', ['_controller' => $controller . 'uptime'])
        );
        foreach (['phpinfo', 'simulateIniOverride', 'dateFormatting'] as $action) {
            $collection->add(
                $routeNamePrefix . u($action)->snake()->toString(),
                new Route($routePathPrefix . u($action)->snake()->replace('_', '-')->toString(), ['_controller' => $controller . $action])
            );
        }

        return $collection;
    }
}
