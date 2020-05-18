<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\UserStatLogin;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use DateTime;

class AuthenticationEventListener implements AuthenticationSuccessHandlerInterface
{
    protected $router;
    protected $security;
    protected $container;
    protected $em;
    protected $tokenStorage;

    public function __construct(Router $router, $container, $entity_manager, TokenStorage $tokenStorage)
    {
        $this->router = $router;
        $this->container = $container;
        $this->em = $entity_manager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if(!$user->hasRole('ROLE_SUPER_ADMIN')){
            $stat = new UserStatLogin();

            $stat->setDateAcces(new DateTime());
            $stat->setUser($user);

            $this->em->persist($stat);

            $this->em->flush();
        }

        $response = new RedirectResponse('myCourses');
        return $response;
    }

}
