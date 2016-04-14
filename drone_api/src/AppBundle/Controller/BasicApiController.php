<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 12:06 PM
 */

namespace AppBundle\Controller;


use AppBundle\Builder\ResponseMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Resources\Strings;

abstract class BasicApiController extends Controller {

    protected function checkTokenInRequest(Request $request){
        $content = $this->getRequestContent($request);
        if (array_key_exists(Strings::$TOKEN,$content)){
            return $this->checkToken($content[Strings::$TOKEN]);
        }
        return false;
    }

    protected function checkToken($token){
        if (strcmp($token , Strings::$TOKEN_KEY)){
            return true;
        }
        return false;
    }
    
    protected function getRequestContent(Request $request){
        if (!empty($request->getContent()))
        {
            return (array)json_decode($request->getContent(), true);
        }
        return array();
    }

    protected function getParamsInContent($request,$key){
        $content = $this->getRequestContent($request);
        if (empty($content)){
            return array();
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

    protected function getStandardResponseFormat(){
        $response = new Response();
        $response->headers->set(Strings::$CONTENT_TYPE, Strings::$CONTENT_TYPE_JSON);
        return $response;
    }

    protected function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }

    protected function getUserById($uid){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($uid);
        return $user;
    }


    protected function getStandard200Response($data , $dataName = "Object", $message = "OK"){
        $response = $this->getStandardResponseFormat();
        $responseBuilder = new ResponseMessageBuilder();
        $responseBuilder->setMessage($message);
        $responseBuilder->setStatus(Strings::$STATUS_OK);
        $responseBuilder->addToParams($data,$dataName);
        $response->setContent($responseBuilder->getResponseJSON());
        return $response;
    }

    protected function getStandardMissingParamResponse(){
        return $this->getResponse(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
    }
    protected function getStandardNotFoundResponse($message = "Not Found"){
        return $this->getResponse($message,Strings::$STATUS_NOT_AUTHENTICATED);
    }

    protected function getTokenNotRightResponse(){
        return $this->getResponse(Strings::$MESSAGE_NO_TOKEN,Strings::$STATUS_NOT_AUTHENTICATED);
    }

    protected function getResponse($message, $code){
        $responseBuilder = new ResponseMessageBuilder();
        $responseBuilder->setMessage($message);
        $responseBuilder->setStatus($code);
        $response = $this->getStandardResponseFormat();
        $response->setContent($responseBuilder->getResponseJSON());
        return $response;
    }
    public static function isMap(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}