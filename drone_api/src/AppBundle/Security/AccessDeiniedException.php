<?php
/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 5:00 PM
 */

namespace AppBundle\Security;

use AppBundle\Resources\Strings;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use \Symfony\Component\HttpFoundation\Request;

class AccessDeniedException implements \Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface{


    /**
     * Handles an access denied failure.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AccessDeniedException $accessDeniedException
     * @return \Symfony\Component\HttpFoundation\Response may return null
     */
    public function handle(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Security\Core\Exception\AccessDeniedException $accessDeniedException)
    {
        $response = new Response();
        $response->headers->set(Strings::$CONTENT_TYPE, Strings::$CONTENT_TYPE_JSON);
        $responseParams = array(Strings::$MESSAGE=>Strings::$MESSAGE_NOT_AUTHENTICATED, Strings::$STATUS=>Strings::$STATUS_NOT_AUTHENTICATED);
        $response->setContent(json_encode($responseParams));
        return $response;
    }

}