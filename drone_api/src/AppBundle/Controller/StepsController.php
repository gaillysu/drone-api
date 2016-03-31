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
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class StepsController extends BasicApiController{

    /**
     * @Route("/steps")
     */
    public function indexAction(){
    }

    /**
     * @Route("/steps/{id}")
     * @Method({"GET"})
     * @param $id
     * Get all the watches from a specific user.
     * @return Response
     */
    public function showAction($id){
    }

    /**
     * @Route("/steps/add")
     * @Method({"POST"})
     * @param $data
     */
    public function createAction(Request $request){

    }

    /**
     * @Route("/steps/edit")
     * @Method({"PUT"})
     * @param $data
     */
    public function updateAction(Request $request){

    }

    /**
     * @Route("/steps/delete")
     * @Method({"DELETE"})
     * @param $id
     */
    public function deleteAction(Request $request){

    }
}