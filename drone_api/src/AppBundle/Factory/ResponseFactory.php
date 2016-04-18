<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 4/18/16
 * Time: 2:15 PM
 */

namespace AppBundle\Factory;


use AppBundle\Builder\ResponseMessageBuilder;
use AppBundle\Resources\Strings;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory {

    public static function makeStandardResponse($completeData = null){
        $response = new Response();
        if($completeData){
            $response->setContent($completeData);
        }
        $response->headers->set(Strings::$CONTENT_TYPE, Strings::$CONTENT_TYPE_JSON);
        return $response;
    }

    public static function makeResponse($message, $code, $requireVersion=true, $data=null, $dataName = ""){
        if (is_object($data)){
            $data = (array)$data;
        }
        $responseBuilder = new ResponseMessageBuilder($message,$code, $data, $dataName);
        return self::makeStandardResponse($responseBuilder->getResponseJSON($requireVersion));
    }

    public static function makeStandard200Response($data , $dataName = "Object", $message = "OK",$requireVersion=true){
        if (is_object($data)){
            $data = (array)$data;
        }
        $responseBuilder = new ResponseMessageBuilder($message,Strings::$STATUS_OK,$data,$dataName);
        return self::makeStandardResponse($responseBuilder->getResponseJSON($requireVersion,$requireVersion));
    }

    public static function makeStandardMissingParamResponse($requireVersion=true){
        return self::makeResponse(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST,$requireVersion);
    }


    public static function makeStandardNotFoundResponse($message = "Not Found",$requireVersion=true){
        return self::makeResponse($message,Strings::$STATUS_NOT_FOUND,$requireVersion);
    }

    public  static function makeTokenNotRightResponse($requireVersion=true){
        return self::makeResponse(Strings::$MESSAGE_NO_TOKEN,Strings::$STATUS_NOT_AUTHENTICATED,$requireVersion);
    }

    public static function makeEmptyOrInvalidResponse($requireVersion=true){
        return self::makeResponse(Strings::$MESSAGE_EMPTY_OR_INVALID,Strings::$STATUS_BAD_REQUEST,$requireVersion);
    }

    public static function makeAccessDeniedResponse(){
        return self::makeResponse(Strings::$MESSAGE_ACCESS_DENIED,Strings::$STATUS_NOT_FOUND);
    }

    public static function makeCoolResponseMessage(){
        return self::makeResponse(Strings::$COOl_MESSAGE,Strings::$STATUS_OK);
    }

    public static function makeCoolAdminResponseMessage(){
        return self::makeResponse(Strings::$COOl_MESSAGE_ADMIN,Strings::$STATUS_OK);
    }

}