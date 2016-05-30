<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 12:06 PM
 */

namespace AppBundle\Controller;


use AppBundle\Factory\ResponseFactory;
use AppBundle\Builder\ResponseMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Resources\Strings;

abstract class BasicApiController extends Controller {

    protected function checkAuth($request, $token = null){
        if (!($this->get(Strings::$AUTH_CHECKER)->isGranted(Strings::$AUTH_GRANTED))){
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $content = $this->getRequestContent($request);
        if (array_key_exists(Strings::$TOKEN,$content)) {
            if (strcmp($content[Strings::$TOKEN], Strings::$TOKEN_KEY)) {
                return null;
            }
        }
        if (strcmp($token, Strings::$TOKEN_KEY)){
            return null;
        }
        return ResponseFactory::makeAccessDeniedResponse();

    }
    
    protected function getParamsInContent($request,$key){
        $content = $this->getRequestContent($request);
        if (empty($content)){

        }
        if (array_key_exists(Strings::$PARAMS,$content)){
            $params = $content[Strings::$PARAMS];
            if (array_key_exists($key,$params)){
                return $params[$key];
            }
        }
        return array();
    }

    protected function requiredRequestContent($keysArray, $content){
        foreach($keysArray as $key){
            if(!array_key_exists($key,$content)){
                return false;
            }
        }
        return true;
    }

    protected function getUserById($uid){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($uid);
        return $user;
    }

    public static function isMap(array $array){
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    private function getRequestContent(Request $request){
        if (!empty($request->getContent())) {
            return (array)json_decode($request->getContent(), true);
        }
        return array();
    }

    private function checkTokenInRequest(Request $request){
        $content = $this->getRequestContent($request);
        if (array_key_exists(Strings::$TOKEN,$content)){
            return strcmp($content[Strings::$TOKEN], Strings::$TOKEN_KEY);
        }
        return false;
    }
}