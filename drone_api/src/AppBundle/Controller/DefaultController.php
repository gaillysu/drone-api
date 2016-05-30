<?php

namespace AppBundle\Controller;

use AppBundle\Factory\ResponseFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BasicApiController
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        return ResponseFactory::makeCoolResponseMessage();
    }

    /**
     * @Route ("/admin")
     * @Method({"GET"})
     * @param Request $request
     * @return Response
     */
    public function adminAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        return ResponseFactory::makeCoolAdminResponseMessage();
    }
}