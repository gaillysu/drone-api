<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Users;
use AppBundle\Resources\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UsersController extends BasicApiController{

    /**
     * @Route("/user")
     */
    public function indexAction(){
    }

    /**
     * @Route("/user/{id}", name="user")
     * @Method({"GET"})
     * @param $id
     * @return Response
     */
    public function showAction($id = -1){
        $response = $this->getStandardResponseFormat();
        if ($id > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APPBUNDLE_USER);
            $user = $repository->find($id);
            if ($user) {
                $responseParams = array(Strings::$MESSAGE=>Strings::$OK, Strings::$STATUS=>Strings::$STATUS_OK);
                $response->setContent($this->getStandardJSONResponse($responseParams,$user,Strings::$USER));
            }else{
                $responseParams = array(Strings::$MESSAGE=>Strings::$MESSAGE_COULD_NOT_FIND_USER, Strings::$STATUS=>Strings::$STATUS_NOT_FOUND);
                $response->setContent(json_encode($responseParams));
            }
            return $response;
        }
        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent($responseParams);
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
        $response = $this->getStandardResponseFormat();
        $userJSON = $this->getParamsInContent($request,Strings::$USER);
            if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD,Strings::$USER_EMAIL,Strings::$USER_FIRST_NAME),$userJSON)) {
                $user = new Users();
                $user->setObject($userJSON);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $responseParams = array(Strings::$MESSAGE=>Strings::$OK, Strings::$STATUS=>Strings::$STATUS_OK);
                $response->setContent($this->getStandardJSONResponse($responseParams,$user,Strings::$USER));
                return $response;
        }
        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent($this->getStandardJSONResponse($responseParams,$response->getContent()));
        return $response;
    }

    /**
     * @Route("/user/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response|void
     * @internal param $data
     */
    public function updateAction(Request $request){
        $response = $this->getStandardResponseFormat();
        $user = $this->getParamsInContent($request,Strings::$USER);
        if (array_key_exists(Strings::$USER_ID,$user)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APPBUNDLE_USER)->find($user[Strings::$USER_ID]);
            if ($foundUser){
                $foundUser->setObject($user);
                $em->flush();
                $responseParams = array(Strings::$MESSAGE=>Strings::$OK, Strings::$STATUS=>Strings::$STATUS_OK);
                $response->setContent($this->getStandardJSONResponse($responseParams,$user));
            }else{
                $responseParams = array(Strings::$MESSAGE=>Strings::$MESSAGE_COULD_NOT_FIND_USER, Strings::$STATUS=>Strings::$STATUS_NOT_FOUND);
                $response->setContent(json_encode($responseParams));
            }
            return $response;
        }
        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent($this->getStandardJSONResponse($responseParams,$response->getContent()));
        return $response;
    }

    /**
     * @Route("/user/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response
     * @internal param $id
     */
    public function deleteAction(Request $request){
        $response = $this->getStandardResponseFormat();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getParamsInContent($request,Strings::$USER);
        if (property_exists($user,Strings::$USER_ID)) {
            $foundUser = $em->getRepository(Strings::$APPBUNDLE_USER)->find($user[Strings::$USER_ID]);
            if ($foundUser) {
                $em->remove($foundUser);
                $em->flush();
                $responseParams = array(Strings::$MESSAGE => Strings::$MESSAGE_DELETED_USER, Strings::$STATUS => Strings::$STATUS_OK);
                $response->setContent($this->getStandardJSONResponse($responseParams, $foundUser));
            } else {

            }
            return $response;
        }

        $responseParams = array(Strings::$MESSAGE=>Strings::$MISSING_PARAMS, Strings::$STATUS=>Strings::$STATUS_BAD_REQUEST);
        $response->setContent($responseParams);
        return $response;
    }

}