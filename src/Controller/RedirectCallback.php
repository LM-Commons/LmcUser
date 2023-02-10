<?php

namespace LmcUser\Controller;

use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\Application;
use Laminas\Router\Exception;
use Laminas\Router\RouteInterface;
use LmcUser\Options\ModuleOptions;

/**
 * Builds a redirect response based on the current routing and parameters
 */
class RedirectCallback
{
    /**
     * @var RouteInterface
     */
    private $router;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ModuleOptions
     */
    private $options;

    /**
     * @param Application    $application
     * @param RouteInterface $router
     * @param ModuleOptions  $options
     */
    public function __construct(Application $application, RouteInterface $router, ModuleOptions $options)
    {
        $this->router = $router;
        $this->application = $application;
        $this->options = $options;
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();
        $redirect = $this->getRedirect($routeMatch->getMatchedRouteName(), $this->getRedirectRouteFromRequest());

        $response = $this->application->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $redirect);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Return the redirect from param.
     * First checks GET then POST
     *
     * @return string|boolean
     */
    private function getRedirectRouteFromRequest()
    {
        $request  = $this->application->getRequest();
        $redirect = $request->getQuery('redirect');
        if ($redirect && ($this->routeMatched($redirect) || $this->routeExists($redirect))) {
            return $redirect;
        }

        $redirect = $request->getPost('redirect');
        if ($redirect && ($this->routeMatched($redirect) || $this->routeExists($redirect))) {
            return $redirect;
        }

        return false;
    }

    /**
     * @param  $route
     * @return bool
     */
    private function routeExists($route)
    {
        try {
            $this->router->assemble([], ['name' => $route]);
        } catch (Exception\RuntimeException $e) {
            return false;
        }
        return true;
    }

    /**
     * @param  string $route
     * @return bool
     */
    private function routeMatched(string $route): bool
    {
        $request = new Request();
        $request->setUri($route);
        return (! is_null($this->router->match($request)));
    }

    /**
     * Returns the url to redirect to based on current route.
     * If $redirect is set and the option to use redirect is set to true, it will return the $redirect url.
     *
     * @param  string $currentRoute
     * @param  bool   $redirect
     * @return mixed
     */
    private function getRedirect($currentRoute, $redirect = false)
    {
        $useRedirect = $this->options->getUseRedirectParameterIfPresent();
        $routeMatched = ($redirect && $this->routeMatched($redirect));
        $routeExists = ($redirect && (! $routeMatched) && $this->routeExists($redirect));
        if (! $useRedirect || ! ($routeMatched || $routeExists)) {
            $redirect = false;
        }

        switch ($currentRoute) {
            case 'lmcuser/register':
            case 'lmcuser/login':
            case 'lmcuser/authenticate':
                if ($redirect && $routeMatched) {
                    return $redirect;
                } else {
                    $route = ($redirect) ?: $this->options->getLoginRedirectRoute();
                    return $this->router->assemble([], ['name' => $route]);
                }
                break;
            case 'lmcuser/logout':
                $route = ($redirect) ?: $this->options->getLogoutRedirectRoute();
                return $this->router->assemble([], ['name' => $route]);
                break;
            default:
                return $this->router->assemble([], ['name' => 'lmcuser']);
        }
    }
}
