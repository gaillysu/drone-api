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
use AppBundle\Util\PBKDF2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UsersController extends BasicApiController{

    /**
     * @Route("/user")
     */
    public function indexAction(){
        if (!$this->checkBasicAuth()) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        return ResponseFactory::makeCoolResponseMessage();
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
                $PBKDF = new PBKDF2();
                $userJSON[Strings::$USER_PASSWORD] = $PBKDF->create_hash($userJSON[Strings::$USER_PASSWORD]);
                $user = new Users();
                $user->setObject($userJSON);
                $em = $this->getDoctrine()->getManager();
                $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($user->email);
                if($foundUser){
                    return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_EXIST, Strings::$STATUS_BAD_REQUEST,true,$user, Strings::$USER);
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
                $foundUser->setPassword(null);
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
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_ID, $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
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

    /**
     * @Route ("user/login")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $authenticated = $this->checkAuth($request);
        if ($authenticated) {
            return $authenticated;
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD, Strings::$USER_EMAIL), $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($userJSON[Strings::$USER_EMAIL]);
            if (!$foundUser) {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
            $PBKDF = new PBKDF2();
            if ($PBKDF->validate_password($userJSON[Strings::$USER_PASSWORD], $foundUser[0]->getPassword())) {
                $foundUser[0]->setPassword(null);
                return ResponseFactory::makeStandard200Response($foundUser, Strings::$USER, Strings::$MESSAGE_USER_LOGGED_IN);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
        }
    }

    /**
     * @Route ("user/forget_password")
     * @Method({"POST"})
     * @param Request $request
     * @return null|Response
     */
    public function forgetPasswordAction(Request $request){
        $authenticated = $this->checkAuth($request);
        if ($authenticated) {
            return $authenticated;
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_ID,Strings::$USER_PASSWORD, Strings::$USER_EMAIL, Strings::$USER_PASSWORD_TOKEN), $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
            if(!$foundUser){
                return ResponseFactory::makeEmptyOrInvalidResponse();
            }
            if($foundUser->getEmail() != $userJSON[Strings::$USER_EMAIL]){
                return ResponseFactory::makeEmptyOrInvalidResponse();
            }
            if($foundUser->getPasswordToken() != $userJSON[Strings::$USER_PASSWORD_TOKEN]){
                return ResponseFactory::makeEmptyOrInvalidResponse();
            }
            $PBKDF = new PBKDF2();
            $foundUser->setPassword($PBKDF->create_hash($userJSON[Strings::$USER_PASSWORD]));
            $foundUser->setPassword($userJSON[Strings::$USER_PASSWORD]);
            $foundUser->setPasswordToken("");
            $em->flush();
            $foundUser->setPassword("");
            return ResponseFactory::makeStandard200Response($foundUser,Strings::$USER);
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/request_password_token")
     * @Method({"POST"})
     * @param Request $request
     * @return null|Response
     */
    public function requestForgetPasswordToken(Request $request){
        $authenticated = $this->checkAuth($request);
        if ($authenticated) {
            return $authenticated;
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_EMAIL), $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($userJSON[Strings::$USER_EMAIL]);
            if (!$foundUser) {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
            $token = openssl_random_pseudo_bytes(16);
            $token = bin2hex($token);
            $foundUser[0]->setPasswordToken($token);
            $em->flush();
            $foundUser[0]->setPassword("");
            return ResponseFactory::makeStandard200Response($foundUser[0]->getForgetPasswordObject(),Strings::$USER);
        }
    }
}