<?php

/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 14/4/2016
 * Time: 11:55 AM
 */

namespace AppBundle\Builder;

use \AppBundle\Resources\Strings;
use \AppBundle\Resources;

class ResponseMessageBuilder
{
    private $response;
    private $message;
    private $status;


    public function __construct($message = "", $status = "",$data= null,$dataName = "object")
    {
        if ($data) {
            if (is_object($data)) {
                $data = (array)$data;
            }
            $data = array_filter($data);
        }
        $this->response = array();
        $this->message = $message;
        $this->status = $status;
        if($data){
            if (self::isMap($data)){
                $this->addToParams($data,$dataName);
            }else{
                foreach ($data as $item){
                    $this->addToParams($item,$dataName);
                }
            }
        }
    }

    public function addToParams($data, $key){
        $data = array_filter($data);
        $params = array();
        if (key_exists($key,$this->response)){
            if(self::isMap($this->response[$key])){
                array_push($params, $this->response[$key]);
            }
        }

        if (empty($params)){
            if (empty($this->response[$key])){
                $this->response[$key] = $data;
            }else{
                $array = $this->response[$key];
                array_push($array,$data);
                $this->response[$key] = $array;
            }

        }else{
            array_push($params, $data);
            $this->response[$key]= $params;
        }
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getResponseArray($requireVersion = true){
        if ($requireVersion){
            $this->response[Strings::$VERSION] = Strings::$VERSION_NUMBER;
        }
        $this->response[Strings::$MESSAGE] = $this->message;
        $this->response[Strings::$STATUS] = $this->status;
        return $this->response;
    }

    public function getResponseJSON($requireVersion = true){
        return json_encode($this->getResponseArray($requireVersion));
    }

    public static function isMap(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}
