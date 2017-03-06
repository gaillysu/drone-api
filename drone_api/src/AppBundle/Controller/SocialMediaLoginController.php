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

class SocialMediaLoginController extends BasicApiController
{

    /**
     * @Route("/user/facebook")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $data
     */
    public function facebookAction(Request $request){
        return $this->createAction($request,true,false);
    }

    /**
     * @Route("/user/twitter")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $data
     */
    public function twitterAction(Request $request){
        return $this->createAction($request,false,true);
    }

    public function createAction(Request $request, $isFacebook, $isTwitter)
    {
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_EMAIL, Strings::$USER_FIRST_NAME), $userJSON)) {
            if (!filter_var($userJSON[Strings::$USER_EMAIL], FILTER_VALIDATE_EMAIL)) {
                return ResponseFactory::makeResponse(Strings::$MESSAGE_EMAIL_INVALID, Strings::$STATUS_BAD_REQUEST);
            }
            $fbExist = false;
            $twitterExist = false;
            if (array_key_exists(Strings::$USER_FACEBOOK_ID, $userJSON)) {
                $fbExist = true;
            }
            if (array_key_exists(Strings::$USER_TWITTER_ID, $userJSON)) {
                $twitterExist = true;
            }
            if (($twitterExist && $fbExist) || (!$twitterExist && !$fbExist) || ($isFacebook && $twitterExist) || ($isTwitter && $fbExist)) {
                return ResponseFactory::makeEmptyOrInvalidResponse();
            }
            $user = new Users();
            $user->setObject($userJSON);
            $user->setVerifiedEmail(false);
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($user->email);
            if ($foundUser) {
                return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_EXIST, Strings::$STATUS_OK);
            }
            $em->persist($user);
            $em->flush();
            $this->generateVerificationTokenForUser($user);
            return ResponseFactory::makeStandard200Response($user, Strings::$USER);
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }


    /**
     * @Route ("user/facebook/login")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function loginFacebook(Request $request){
        return $this->loginAction($request, true, false);
    }

    /**
     * @Route ("user/twitter/login")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function loginTwitter(Request $request){
        return $this->loginAction($request, false, true);
    }


    public function loginAction(Request $request, $isFacebook, $isTwitter)
    {
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        $fbExist = false;
        $twitterExist = false;
        $key = "";
        if (array_key_exists(Strings::$USER_FACEBOOK_ID, $userJSON)) {
            $fbExist = true;
            $key = Strings::$USER_FACEBOOK_ID;
        }
        if (array_key_exists(Strings::$USER_TWITTER_ID, $userJSON) && !$fbExist) {
            $twitterExist = true;
            $key = Strings::$USER_TWITTER_ID;
        }
        if (($twitterExist && $fbExist) || (!$twitterExist && !$fbExist) || ($isFacebook && $twitterExist) || ($isTwitter && $fbExist)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (array_key_exists(Strings::$USER_EMAIL,$userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findByEmail($userJSON[Strings::$USER_EMAIL]);
            if (!$foundUser) {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
            $foundUser = $foundUser[0];
            $authenticated = false;
            if ($isFacebook && strcmp($foundUser->getFacebookId(), $userJSON[$key]) == 0){
                $authenticated = true;
            } else if ($isTwitter && strcmp($foundUser->getTwitterId(), $userJSON[$key]) == 0 ){
                $authenticated = true;
            }
            if ($authenticated){
                if (!$foundUser->getVerifiedEmail()) {
                    $emailVerificationTokens = $em->getRepository(Strings::$APP_BUNDLE_EMAIL_VERIFICATION_TOKEN)->findByUid($foundUser->getId());
                    if (empty($emailVerificationTokens)) {
                        $this->generateVerificationTokenForUser($foundUser);
                    }
                }
                return ResponseFactory::makeStandard200Response($foundUser, Strings::$USER, Strings::$MESSAGE_USER_LOGGED_IN);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/wechat")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function registerWeChatAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_WECHAT_ID, Strings::$USER_FIRST_NAME), $userJSON)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findBywechat($userJSON[Strings::$USER_WECHAT_ID]);
            if ($foundUser){
                return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_EXIST, Strings::$STATUS_OK);
            }
            $user = new Users();
            $user->setObject($userJSON);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $user->setVerifiedEmail(false);
            return ResponseFactory::makeStandard200Response($user,Strings::$USER);
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/wechat/login")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function loginWeChatAction(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_WECHAT_ID), $userJSON)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findBywechat($userJSON[Strings::$USER_WECHAT_ID]);
            if (!$foundUser){
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST_OR_PASSWORD_WRONG);
            }
            return ResponseFactory::makeStandard200Response($foundUser[0], Strings::$USER, Strings::$MESSAGE_USER_LOGGED_IN);
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route ("user/wechat/check")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function checkWeChatAction(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        if (empty($userJSON)) {
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if ($this->requiredRequestContent(array(Strings::$USER_WECHAT_ID), $userJSON)) {
            $em = $this->getDoctrine()->getManager();
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->findBywechat($userJSON[Strings::$USER_WECHAT_ID]);
            if ($foundUser) {
                return ResponseFactory::makeStandard200Response($userJSON, Strings::$USER);
            } else {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }
}