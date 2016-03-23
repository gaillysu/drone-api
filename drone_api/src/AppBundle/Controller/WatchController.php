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

class WatchController extends BasicApiController{

    /**
     * @Route("/watch")
     */
    public function indexAction(){

    }

    /**
     * @Route("/watch/{id}")
     * @Method({"GET"})
     * @param $id
     * Get all the watches from a specific user.
     */
    public function showAction($id){

    }

    /**
     * @Route("/watch/add")
     * @Method({"POST"})
     * @param $data
     */
    public function createAction(Request $request){

    }

    public function updateAction(Request $request){
        // Does not exist
    }

    /**
     * @Route("/watch/delete")
     * @Method({"DELETE"})
     * @param $id
     */
    public function deleteAction(Request $request){

    }
}