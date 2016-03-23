<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Resources\Strings;

class UsersController extends BasicApiController{

    /**
     * @Route("/user")
     */
    public function indexAction(){

        return parent::indexAction(false);
    }

    /**
     * @Route("/user/{id}", name="user")
     * @Method({"GET"})
     * @param $id
     */
    public function showAction($id = 0){
        $response = new Response();
        $response->headers->set(Strings::$CONTENT_TYPE, Strings::$CONTENT_TYPE_JSON);
        if ($id != 0) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Users');
            $user = $repository->find($id);
            if ($user) {
                $responseParams = array(Strings::$MESSAGE=>Strings::$OK, Strings::$STATUS=>Strings::$STATUS_FOUND);
                $response->setContent(json_encode($this->mergeData($responseParams,$user)));
                return $response;
            }else{
                $responseParams = array(Strings::$MESSAGE=>Strings::$MESSAGE_COULD_NOT_FIND_USER, Strings::$STATUS=>Strings::$STATUS_NOT_FOUND);
                $response->setContent(json_encode($responseParams,$response->getContent()));
                return $response;
            }
        }

        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent(json_encode($this->mergeData($responseParams,$response->getContent())));
        return $response;
    }

    /**
     * @Route("/user/create")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $response = new Response();
        $response->headers->set(Strings::$CONTENT_TYPE, Strings::$CONTENT_TYPE_JSON);
        $content = $this->getRequestContent($request);
        if (array_key_exists(Strings::$PARAMS,$content)){
            if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD,Strings::$USER_EMAIL,Strings::$USER_FIRST_NAME),$content) || !empty($content)) {
                $params = $content[Strings::$PARAMS];

                if (array_key_exists(Strings::$USER, $params)){
                    $userJSON = $params[Strings::$USER];
                    $user = new Users();
                    $user->setEmail($userJSON[Strings::$USER_EMAIL]);
                    $user->setPassword($userJSON[Strings::$USER_PASSWORD]);
                    $user->setFirstName($userJSON[Strings::$USER_FIRST_NAME]);
                    if (array_key_exists(Strings::$USER_AGE, $userJSON)) {
                        $user->setFirstName($userJSON[Strings::$USER_AGE]);
                    }
                    if (array_key_exists(Strings::$USER_LAST_NAME, $userJSON)) {
                        $user->setFirstName($userJSON[Strings::$USER_LAST_NAME]);
                    }
                    if (array_key_exists(Strings::$USER_LAST_LONGITUDE, $userJSON)) {
                        $user->setFirstName($userJSON[Strings::$USER_LAST_LONGITUDE]);
                    }
                    if (array_key_exists(Strings::$USER_LAST_LATITUDE, $userJSON)) {
                        $user->setFirstName($userJSON[Strings::$USER_LAST_LATITUDE]);
                    }
                    if (array_key_exists(Strings::$USER_LENGTH, $userJSON)) {
                        $user->setFirstName($userJSON[Strings::$USER_LENGTH]);
                    }
                    $em = $this->getDoctrine()->getManager();

                    $em->persist($user);
                    $em->flush();
                }
                $responseParams = array(Strings::$MESSAGE=>Strings::$OK, Strings::$STATUS=>Strings::$STATUS_OK);
                $response->setContent(json_encode($this->mergeData($user, $responseParams)));
                return $response;
            }
        }
        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent(json_encode($this->mergeData($responseParams,$response->getContent())));
        return $response;
    }

    /**
     * @Route("/user/edit")
     * @Method({"PUT"})
     * @param $data
     */

    public function updateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->find($id);
        if (!$user) {
        }
        $em->flush();
    }

    /**
     * @Route("/user/delete")
     * @Method({"DELETE"})
     * @param $id
     */
    public function deleteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->find($id);
        if(!$user){

        }
        $em->remove($user);
        $em->flush();
    }
}