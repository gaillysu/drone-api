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
        if ($id > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APPBUNDLE_USER);
            $user = $repository->find($id);
            if ($user) {
                return $this->getStandard200Response($user,Strings::$USER);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/user/create")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $userJSON = $this->getParamsInContent($request,Strings::$USER);
            if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD,Strings::$USER_EMAIL,Strings::$USER_FIRST_NAME),$userJSON)) {
                $user = new Users();
                $user->setObject($userJSON);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->getStandard200Response($user,Strings::$USER);
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/user/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response|void
     * @internal param $data
     */
    public function updateAction(Request $request){
        $user = $this->getParamsInContent($request,Strings::$USER);
        if (array_key_exists(Strings::$USER_ID,$user)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APPBUNDLE_USER)->find($user[Strings::$USER_ID]);
            if ($foundUser){
                $foundUser->setObject($user);
                $em->flush();
                return $this->getStandard200Response($user,Strings::$USER);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return $this->getStandardMissingParamResponse();

    }

    /**
     * @Route("/user/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response
     * @internal param $id
     */
    public function deleteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getParamsInContent($request,Strings::$USER);
        if (property_exists($user,Strings::$USER_ID)) {
            $foundUser = $em->getRepository(Strings::$APPBUNDLE_USER)->find($user[Strings::$USER_ID]);
            if ($foundUser) {
                $em->remove($foundUser);
                $em->flush();
                return $this->getStandard200Response($foundUser,Strings::$USER,Strings::$MESSAGE_DELETED_USER);
            } else {
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_WATCH);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

}