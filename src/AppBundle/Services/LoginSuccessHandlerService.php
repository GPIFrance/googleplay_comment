<?php
/**
 * Created by PhpStorm.
 * User: JEC
 * Date: 06/12/2016
 * Time: 11:29
 */

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandlerService implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if (in_array("ROLE_ADMIN", $token->getUser()->getRoles())) {
            return new RedirectResponse($this->router->generate('app_admin_users'));
        } else {
            return new RedirectResponse($this->router->generate('app_home_apps'));
        }
    }
}