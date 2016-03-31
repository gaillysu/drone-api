<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 12:06 PM
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Resources\Strings;
abstract class BasicApiController extends Controller {

    protected function validateRequest(Request $request){
        if($request){
            return true;
        }
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

    protected function getStandardJSONResponse($standardResponse, $object, $key ="object"){
        $object = (object) array_filter((array) $object);
        $jsonObject = json_encode($this->utf8ize($object));
        $arrayFromObject = (array) json_decode($jsonObject);
        $standardResponse[$key] = $arrayFromObject;
        return json_encode($standardResponse);
    }

    public function indexAction(){
        return $this->getStandardResponseFormat(false,404);
    }

    public function showAction($id){
        return $this->getStandardResponseFormat(false,404);
    }

    public function createAction(Request $request){
        return $this->getStandardResponseFormat(false,404);
    }

    public function deleteAction(Request $request){
        return $this->getStandardResponseFormat(false,404);
    }

    public function updateAction(Request $request){
        return $this->getStandardResponseFormat(false,404);
    }

    protected function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
}