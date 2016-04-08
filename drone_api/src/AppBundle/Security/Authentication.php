<?php
/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 9:47 AM
 */

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\FirewallMap;

class Authentication{

    private $tokenStorage;
    private $providers;
    private $authenticationManager;
    private $accessDecisionManager;
    private $authorizationChecker;
    private $firewallMap;
    private $requestMatcher;
    private $authenticationListeners;
    private $firewall;
    private $dispatcher;

    public function __construct()
    {
        $this->tokenStorage = new TokenStorage();
        $this->providers = array();
        $this->authenticationManager = new AuthenticationProviderManager($this->providers);
        $this->accessDecisionManager = new AccessDecisionManager();
        $this->authorizationChecker = new AuthorizationChecker($this->tokenStorage,$this->authenticationManager, $this->accessDecisionManager);
        $this->firewallMap = new FirewallMap();
        $this->requestMatcher = new RequestMatcher('^/secured-area/');
        $this->authenticationListeners = array(new BasicAuthListener($this->tokenStorage, $this->authenticationManager, "Super_cool_token"));
        $exceptionListener = new ExceptionListener($this->tokenStorage);
        $this->firewallMap->add($this->requestMatcher,$this->authenticationListeners,$exceptionListener);
        $this->dispatcher = new EventDispatcher();
        $this->firewall = new Firewall($this->firewallMap,$this->dispatcher);
        $this->dispatcher->addListener(
            KernelEvents::REQUEST,
            array($this->firewall,'onKernelRequest')
        );
    }


    
}


