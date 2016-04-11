<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Resources\Strings;
use AppBundle\Entity\Sleep;

class SleepController extends BasicApiController{

    /**
     * @Route("/sleep")
     */
    public function indexAction(){
    }

    /**
     * @Route("/sleep/user/{uid}")
     * @Method({"GET"})
     * @param int $uid
     * Get all the watches from a specific user.
     * @return Response
     * @internal param $offset
     */
    public function showAction($uid = -1){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
//        if (!$this->checkToken($token)){
//            return $this->getTokenNotRightResponse();
//        }
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_SLEEP);
            $sleepArray = $repository->findByUid($uid);
            if ($sleepArray) {
                return $this->getStandard200Response($sleepArray,Strings::$SLEEP);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/sleep/create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function createAction(Request $request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        if ($this->checkTokenInRequest($request)){
            return $this->getTokenNotRightResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_SLEEP);
        $sleepJson = $this->getParamsInContent($request,Strings::$SLEEP);
        if ($this->requiredRequestContent(array(Strings::$SLEEP_USER_ID,Strings::$SLEEP_DATE, Strings::$SLEEP_WAKE_TIME,Strings::$SLEEP_LIGHT_SLEEP, Strings::$SLEEP_DEEP_SLEEP),$sleepJson)) {
            $timeMidnight = strtotime("0:00",$sleepJson[Strings::$SLEEP_DATE]);
            $user = $this->getUserById($sleepJson[Strings::$SLEEP_USER_ID]);

            $sleepArray = $repository->findByUid($sleepJson[Strings::$SLEEP_USER_ID]);
            if(!$user){
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            } else if (gmdate($timeMidnight) != gmdate($sleepJson[Strings::$SLEEP_DATE])){
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_DATE_NOT_RIGHT);
            } else if($sleepArray){
                foreach ($sleepArray as $sleepDay){
                    if ($sleepDay->getDate() == gmdate($timeMidnight)){
                        $sleepDay->setObject($sleepJson);
                        $em->flush();
                        return $this->getStandard200Response($sleepArray,Strings::$SLEEP,Strings::$MESSAGE_SLEEP_DATA_ALREADY_EXIST_UPDATED_INSTEAD);
                    }
                }
            }
            $sleep = new Sleep();
            $sleep ->setObject($sleepJson);
            $em->persist($sleep);
            $em->flush();
            return $this->getStandard200Response($sleep,Strings::$SLEEP);
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/sleep/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function updateAction(Request $request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        if ($this->checkTokenInRequest($request)){
            return $this->getTokenNotRightResponse();
        }
        $sleep = $this->getParamsInContent($request,Strings::$SLEEP);
        if (array_key_exists(Strings::$SLEEP_ID,$sleep)) {
            $em = $this->getDoctrine()->getManager();
            $foundSleep = $em->getRepository(Strings::$APP_BUNDLE_SLEEP)->find($sleep[Strings::$SLEEP_ID]);
            if ($foundSleep){
                $foundSleep->setObject($sleep);
                $em->flush();
                return $this->getStandard200Response($foundSleep,Strings::$SLEEP);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/sleep/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response|void
     * @internal param $id
     */
    public function deleteAction(Request $request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        if ($this->checkTokenInRequest($request)){
            return $this->getTokenNotRightResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $sleep = $this->getParamsInContent($request,Strings::$SLEEP);
        if (array_key_exists(Strings::$SLEEP_ID,$sleep)) {
            $foundSleep = $em->getRepository(Strings::$APP_BUNDLE_SLEEP)->find($sleep[Strings::$SLEEP_ID]);
            if ($foundSleep) {
                $em->remove($foundSleep);
                $em->flush();
                return $this->getStandard200Response($foundSleep,Strings::$SLEEP,Strings::$MESSAGE_DELETED_SLEEP);
            } else {
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP);
            }
        }
        return $this->getStandardMissingParamResponse();
    }
}