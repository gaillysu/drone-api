<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 7/6/16
 * Time: 3:13 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\EmailVerificationToken;
use AppBundle\Factory\ResponseFactory;
use AppBundle\Resources\Strings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class EmailVerificationController extends BasicApiController{

    /**
     * @Route ("user/resend_email_token")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function requestNewEmailVerificationToken(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $userJSON = $this->getParamsInContent($request, Strings::$USER);
        $em = $this->getDoctrine()->getManager();
        if ($this->requiredRequestContent(array(Strings::$USER_ID), $userJSON)) {
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($userJSON[Strings::$USER_ID]);
            if (!$foundUser) {
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_USER_NOT_EXIST);
            }
            if($foundUser->getVerifiedEmail()){
                return ResponseFactory::makeResponse(Strings::$MESSAGE_USER_NOT_CHANGED_EMAIL, Strings::$STATUS_BAD_REQUEST);
            }
            $this->generateVerificationTokenForUser($foundUser);
            return ResponseFactory::makeStandard200Response(null);
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route("verify/{token}")
     * @Method({"GET"})
     * Get all the watches from a specific user.
     * @return Response
     * @internal param Request $request
     * @internal param $offset
     */
    public function verifyEmail(Request $request, $token){
        $em = $this->getDoctrine()->getManager();
        $emailVerificationToken = $em->getRepository(Strings::$APP_BUNDLE_EMAIL_VERIFICATION_TOKEN)->findByToken($token);
        if($emailVerificationToken){
            $foundUser = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($emailVerificationToken[0]->getUid());
            if($foundUser){
                if(!$foundUser->getVerifiedEmail()){
                    $foundUser->setVerifiedEmail(true);
                    $em->remove($emailVerificationToken[0]);
                    $em->flush();
                    return new Response($this->renderView('AppBundle:emails:registration_complete.html.twig', array()));
                }
            }
        }
        return new Response($this->renderView('AppBundle:emails:registration_failed.html.twig', array()));
    }

    private function generateVerificationTokenForUser($user){
        $em = $this->getDoctrine()->getManager();
        $emailVerificationToken = $em->getRepository(Strings::$APP_BUNDLE_EMAIL_VERIFICATION_TOKEN)->findByUid($user->getId());
        $template = "AppBundle:emails:email_registration.html.twig";
        if(is_array($emailVerificationToken)){
            $emailVerificationToken = $emailVerificationToken[0];
        }
        if($emailVerificationToken != null){
            $emailVerificationToken->setToken(md5(uniqid(rand())));
            $template = "AppBundle:emails:email_retry_registration.html.twig";
        }else{
            $emailVerificationToken = new EmailVerificationToken();
            $emailVerificationToken->setToken(md5(uniqid(rand())));
            $emailVerificationToken->setUid($user->getId());
            $em->persist($emailVerificationToken);
        }
        $em->flush();
        $message = \Swift_Message::newInstance()
            ->setSubject(Strings::$GENERATE_TOKEN_EMAIL_SUBJECT)
            ->setFrom(Strings::$GENERATE_TOKEN_EMAIL_FROM)
            ->setTo($user->getEmail())
            ->setBody($this->renderView($template, array('name' => $user->getFirstName(),
                "link"=>"http://drone.karljohnchow.com/verify/".$emailVerificationToken->getToken()) , 'text/html'));
        $this->get('mailer')->send($message);
    }



}