<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Factory\ResponseFactory;
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        return ResponseFactory::makeCoolResponseMessage();
    }

    /**
     * @Route("/user/{id}", name="user")
     * @Method({"GET"})
     * @param $id
     * @return Response
     */
    public function showAction($id = -1){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        if ($id > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_USER);
            $user = $repository->find($id);
            if ($user) {
                return ResponseFactory::makeStandard200Response($user,Strings::$USER);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route("/user/create")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }
        $userJSON = $this->getParamsInContent($request,Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
            if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD,Strings::$USER_EMAIL,Strings::$USER_FIRST_NAME),$userJSON)) {
                $user = new Users();
                $user->setObject($userJSON);
                $em = $this->getDoctrine()->getManager();
                $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($user->email);
                if($foundUser){
                    return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_EXIST, Strings::$STATUS_BAD_REQUEST,true,$userJSON, Strings::$USER);
                }
                $em->persist($user);
                $em->flush();
                return ResponseFactory::makeStandard200Response($user,Strings::$USER);
            }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route("/user/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response|void
     * @internal param $data
     */
    public function updateAction(Request $request){
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }

        $userJSON = $this->getParamsInContent($request,Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_ID,$userJSON)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
            if ($foundUser){
                $foundUser->setObject($userJSON);
                $em->flush();
                return ResponseFactory::makeStandard200Response($userJSON,Strings::$USER);
            }else{
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();

    }

    /**
     * @Route("/user/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response
     * @internal param $id
     */
    public function deleteAction(Request $request)
    {
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }
        $em = $this->getDoctrine()->getManager();
        $userJson = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJson)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_ID, $userJson)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJson[Strings::$USER_ID]);
            if ($foundUser) {
                $em->remove($foundUser);
                $em->flush();
                return ResponseFactory::makeStandard200Response($foundUser,Strings::$USER, Strings::$MESSAGE_DELETED_USER);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }
}