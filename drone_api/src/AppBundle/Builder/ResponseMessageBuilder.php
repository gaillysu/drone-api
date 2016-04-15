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


    public function __construct()
    {
        $this->response = array();
        $this->response[Strings::$VERSION] = Strings::$VERSION_NUMBER;
    }

    public function addToParams($data, $key){
        $params = array();
        if (key_exists($key,$this->response)){
            if(self::isMap($this->response[$key])){
                array_push($params, $this->response[$key]);
            }
        }

        if (empty($params)){
            $this->response[$key] = $data;
        }else{
            array_push($params, $data);
            $this->response[$key]= $params;
        }
    }

    public function getResponseArray(){
        $this->response[Strings::$MESSAGE] = $this->message;
        $this->response[Strings::$STATUS] = $this->status;
        return $this->response;
    }

    public function getResponseJSON(){
        return json_encode($this->getResponseArray());
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public static function isMap(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}
