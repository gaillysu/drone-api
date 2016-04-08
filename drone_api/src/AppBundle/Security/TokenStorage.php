<?php
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 9:42 AM
 */
class TokenStorage implements \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
{

    /**
     * Returns the current security token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken()
    {
        // TODO: Implement getToken() method.
    }

    /**
     * Sets the authentication token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null)
    {
        // TODO: Implement setToken() method.
    }
}