<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\EmailVerificationToken;
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
     * @Method({"GET"})
     * @Route("/user")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request){
        if (!$this->isAuthenticated($request)) {
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
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request,Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
            if ($this->requiredRequestContent(array(Strings::$USER_PASSWORD,Strings::$USER_EMAIL,Strings::$USER_FIRST_NAME),$userJSON)) {
                if (!filter_var($userJSON[Strings::$USER_EMAIL], FILTER_VALIDATE_EMAIL)) {
                    return ResponseFactory::makeResponse(Strings::$MESSAGE_EMAIL_INVALID,Strings::$STATUS_BAD_REQUEST);
                }
                $PBKDF = new PBKDF2();
                $userJSON[Strings::$USER_PASSWORD] = $PBKDF->create_hash($userJSON[Strings::$USER_PASSWORD]);
                $user = new Users();
                $user->setObject($userJSON);
                $em = $this->getDoctrine()->getManager();
                $user->setVerifiedEmail(false);
                $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($user->email);
                if($foundUser){
                    return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_EXIST, Strings::$STATUS_BAD_REQUEST);
                }
                $em->persist($user);
                $em->flush();
                $this->generateVerificationTokenForUser($user);
                $user->setPassword(null);
                $em->remove($user);
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
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request,Strings::$USER);
        if(empty($userJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_ID,$userJSON)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
            if ($foundUser){
                if ($foundUser->getId() != $userJSON[Strings::$USER_ID]){
                    return ResponseFactory::makeResponse(Strings::$MESSAGE_EMAIL_ALREADY_TAKEN, Strings::$STATUS_BAD_REQUEST);
                }
                if(array_key_exists(Strings::$USER_EMAIL,$userJSON)){
                    if(strcmp($userJSON[Strings::$USER_EMAIL],$foundUser->getEmail()) !== 0) {
                        if (!filter_var($userJSON[Strings::$USER_EMAIL], FILTER_VALIDATE_EMAIL)) {
                            return ResponseFactory::makeResponse(Strings::$MESSAGE_EMAIL_INVALID, Strings::$STATUS_BAD_REQUEST);
                        }
                        $foundUser->setVerifiedEmail(false);
                        $this->generateVerificationTokenForUser($foundUser);
                    }
                }
                $foundUser->setObject($userJSON);
                $em->flush();
                $foundUser->setPassword(null);
                return ResponseFactory::makeStandard200Response($foundUser,Strings::$USER);
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
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_ID, $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
            if ($foundUser) {
                $sleepArray = $em->getRepository(Strings::$APP_BUNDLE_SLEEP)->findByUid($foundUser->getId());
                foreach($sleepArray as $sleep){
                    $em->remove($sleep);
                    $em->flush();
                }
                $stepArray = $em->getRepository(Strings::$APP_BUNDLE_STEPS)->findByUid($foundUser->getId());
                foreach($stepArray as $steps){
                    $em->remove($steps);
                    $em->flush();
                }

                $watchArray = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->findByUid($foundUser->getId());
                foreach($watchArray as $watch){
                    $em->remove($watch);
                    $em->flush();
                }
                $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
                $em->remove($foundUser);
                $em->flush();
                $foundUser->setPassword(null);
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
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
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
                if (!$foundUser[0]->getVerifiedEmail()) {
                    $emailVerificationTokens = $em->getRepository(Strings::$APP_BUNDLE_EMAIL_VERIFICATION_TOKEN)->findByUid($foundUser[0]->getId());
                    if (empty($emailVerificationTokens)) {
                        $this->generateVerificationTokenForUser($foundUser[0]);
                    }
                }
                $foundUser[0]->setPassword(null);
                return ResponseFactory::makeStandard200Response($foundUser[0], Strings::$USER, Strings::$MESSAGE_USER_LOGGED_IN);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/forget_password")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function forgetPasswordAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
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
     * @return Response
     */
    public function requestForgetPasswordToken(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
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
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/check_email")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function checkEmail(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if ($this->requiredRequestContent(array(Strings::$USER_EMAIL), $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($userJSON[Strings::$USER_EMAIL]);
            if ($foundUser) {
                return ResponseFactory::makeStandard200Response($userJSON, Strings::$USER);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }
}