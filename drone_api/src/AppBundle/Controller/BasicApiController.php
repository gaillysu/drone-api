<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 12:06 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\EmailVerificationToken;
use AppBundle\Factory\ResponseFactory;
use AppBundle\Builder\ResponseMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Resources\Strings;

abstract class BasicApiController extends Controller {

    protected function isAuthenticated($request){
        if (!($this->get(Strings::$AUTH_CHECKER)->isGranted(Strings::$AUTH_GRANTED))){
            return false;
        }
        $content = $this->getRequestContent($request);
        if (array_key_exists(Strings::$TOKEN,$content)) {
            if (strcmp($content[Strings::$TOKEN], Strings::$TOKEN_KEY) == 0) {
                return true;
            }
        }
        if (strcmp($request->query->get(Strings::$TOKEN), Strings::$TOKEN_KEY) == 0){
            return true;
        }
        return false;
    }
    
    protected function getParamsInContent($request,$key){
        $content = $this->getRequestContent($request);
        if (empty($content)){

        }
        if (array_key_exists(Strings::$PARAMS,$content)){
            $params = $content[Strings::$PARAMS];
            if (array_key_exists($key,$params)){
                return $params[$key];
            }
        }
        return array();
    }

    protected function requiredRequestContent($keysArray, $content){
        foreach($keysArray as $key){
            if(!array_key_exists($key,$content)){
                return false;
            }
        }
        return true;
    }

    protected function getUserById($uid){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Strings::$APP_BUNDLE_USER)->find($uid);
        return $user;
    }

    public static function isMap(array $array){
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    private function getRequestContent(Request $request){
        if (!empty($request->getContent())) {
            return (array)json_decode($request->getContent(), true);
        }
        return array();
    }


    public function generateVerificationTokenForUser($user){
        $em = $this->getDoctrine()->getManager();
        $emailVerificationTokens = $em->getRepository(Strings::$APP_BUNDLE_EMAIL_VERIFICATION_TOKEN)->findByUid($user->getId());
        $template = "AppBundle:emails:email_registration.html.twig";
        $emailVerificationToken = null;
        if(is_array($emailVerificationTokens)){
            if(!empty($emailVerificationTokens)){
                $emailVerificationToken = $emailVerificationTokens[0];
            }
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
            ->setContentType("text/html")
            ->setBody($this->renderView($template, array('name' => $user->getFirstName(),
                "link"=>Strings::$VERIFY_URL.$emailVerificationToken->getToken())
            ,'text/html'));
        $mailLogger = new \Swift_Plugins_Loggers_ArrayLogger();

        $mailer = $this->get('mailer');
        $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mailLogger));
        $mailer->send($message);
//        if ($mailer->send($message)) {
//            echo '[SWIFTMAILER] sent email to ' . $user->getEmail();
//        } else {
//            echo '[SWIFTMAILER] not sending email: ' . $mailLogger->dump();
//        }
    }
}