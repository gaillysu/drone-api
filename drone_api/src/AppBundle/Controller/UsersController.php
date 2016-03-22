<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UsersController extends BasicApiController{

    /**
     * @Route("/user")
     */
    public function indexAction(){
        if($this->validateRequest(true)){

        }
        parent::indexAction(false);
    }

    /**
     * @Route("/user/{id}", name="user")
     * @Method({"GET"})
     * @param $id
     */
    public function showAction($id){
        if($this->validateRequest(true)){
            $response = $this->getStandardResponse($id,200);
            $append = array('id'=>$id);
            $response->setContent(json_encode($this->mergeData($append,$response->getContent())));
            return $response;
        }
        parent::showAction($id);
    }

    /**
     * @Route("/user/create")
     * @Method({"POST"})
     * @param $data
     */
    public function createAction($data){
        if($this->validateRequest($data)){

        }
        parent::createAction($data);
    }

    /**
     * @Route("/user/edit")
     * @Method({"PUT"})
     * @param $data
     */
    public function updateAction($data){
        if($this->validateRequest($data)){

        }
        parent::updateAction($data);
    }

    /**
     * @Route("/user/delete")
     * @Method({"DELETE"})
     * @param $id
     */
    public function deleteAction($id){
        if($this->validateRequest($id)){

        }
        parent::validateRequest($id);
    }
}