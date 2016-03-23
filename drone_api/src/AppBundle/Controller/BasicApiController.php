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

    protected function requiredRequestContent($keysArray, $content){
        foreach($keysArray as $key){
            if(!array_key_exists($key,$content)){
                return false;
            }
        }
        return true;
    }

//    protected function getStandardResponse($validated = false, $statusCode = 404){
//        $response = new Response();
//        $response->headers->set('Content-Type', 'application/json');
//        $response->setStatusCode($statusCode);
//        if($validated){
//            $response->setContent(json_encode(array('status_code' => $statusCode)));
//        }else{
//            $response->setContent(json_encode(array(
//                'error_message' => 'Could not validate request',
//                'status_code' => $statusCode)));
//        }
//        return $response;
//    }

    protected function mergeData($appendingArray,$jsonEncodedArray){
        $originalArray = (array) json_decode($jsonEncodedArray);
        return array_merge($originalArray, $appendingArray);
    }

    public function indexAction(){
        return $this->getStandardResponse(false,404);
    }

    public function showAction($id){
        return $this->getStandardResponse(false,404);
    }

    public function createAction(Request $request){
        return $this->getStandardResponse(false,404);
    }

    public function deleteAction(Request $request){
        return $this->getStandardResponse(false,404);
    }

    public function updateAction(Request $request){
        return $this->getStandardResponse(false,404);
    }

}