<?php
/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 11:42 AM
 */


use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\Firewall\AccessListener;


$voters = array();

$strategy;

$allowIfAllAbstainDecisions;

$allowIfEqualGrantedDeniedDecisions;

$accessDecisionManager =  new AccessDecisionManager(
    $this->voters,
    $this->strategy,
    $this->allowIfAllAbstainDecisions,
    $this->allowIfEqualGrantedDeniedDecisions
);

$anonymousClass = 'Symfony\Component\Security\Core\Authentication\Token\AnonymousToken';
$rememberMeClass = 'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken';

$trustResolver = new AuthenticationTrustResolver($anonymousClass, $rememberMeClass);

$authenticatedVoter = new AuthenticatedVoter($trustResolver);

$token = 'super_nice_token';

$object;

$vote = $authenticatedVoter->vote($token, $object, array('IS_AUTHENTICATED_FULLY'));

$roleVoter = new RoleVoter('ROLE_');

$roleVoter->vote($token, $object, array('ROLE_ADMIN'));


$hierarchy = array(
    'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_USER'),
);

$roleHierarchy = new RoleHierarchy($hierarchy);

$roleHierarchyVoter = new RoleHierarchyVoter($roleHierarchy);

$role = new Role('ROLE_ADMIN');

var_dump($role->getRole());


$accessMap = new AccessMap();
$requestMatcher = new RequestMatcher('^/admin');
$accessMap->add($requestMatcher, array('ROLE_ADMIN'));

$accessListener = new AccessListener(
    $securityContext,
    $accessDecisionManager,
    $accessMap,
    $authenticationManager
);

$authorizationChecker = new AuthorizationChecker(
    $tokenStorage,
    $authenticationManager,
    $accessDecisionManager
);

if (!$authorizationChecker->isGranted('ROLE_ADMIN')) {
    throw new AccessDeniedException();
}