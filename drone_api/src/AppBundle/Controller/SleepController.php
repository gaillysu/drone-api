<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Factory\ResponseFactory;
use AppBundle\Builder\ResponseMessageBuilder;
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
     * @Route("/sleep/user/{uid}")
     * @Method({"GET"})
     * @param int $uid
     * Get all the watches from a specific user.
     * @param Request $request
     * @return Response
     * @internal param $offset
     */
    public function showAction($uid = -1, Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_SLEEP);
            if($request->query->get(Strings::$START_DATE) && $request->query->get(Strings::$END_DATE)) {
                $start = new \DateTime();
                $start->setTimestamp($request->query->get(Strings::$START_DATE));
                $end = new \DateTime();
                $end->setTimestamp($request->query->get(Strings::$END_DATE));
                $query = $repository->createQueryBuilder('s')
                    ->where("s.uid = :uid AND s.date BETWEEN :" . Strings::$START_DATE . " AND :" . Strings::$END_DATE)
                    ->setParameter(Strings::$START_DATE, $start->format(Strings::$DATE_FORMAT))
                    ->setParameter(Strings::$END_DATE, $end->format(Strings::$DATE_FORMAT))
                    ->setParameter(Strings::$SLEEP_USER_ID, $uid)
                    ->setMaxResults(50)
                    ->getQuery();
                $sleepArray = $query->getResult();
            }else{
                $sleepArray = $repository->findByUid($uid, null, 10);
            }
            if ($sleepArray) {
                return ResponseFactory::makeStandard200Response($sleepArray,Strings::$SLEEP);
            }else{
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }


    /**
     * @Route("/sleep/create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function createAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $sleepJSON = $this->getParamsInContent($request,Strings::$SLEEP);
        if(empty($sleepJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if(self::isMap($sleepJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->createSleep($sleepJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($sleepJSON as $sleep){
            $responseMessage->addToParams($this->createSleep($sleep,false),Strings::$SLEEP);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    private function createSleep($json,$versionRequired){
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_SLEEP);
        if ($this->requiredRequestContent(array(Strings::$SLEEP_USER_ID,Strings::$SLEEP_DATE, Strings::$SLEEP_WAKE_TIME,Strings::$SLEEP_LIGHT_SLEEP, Strings::$SLEEP_DEEP_SLEEP),$json)) {
            $user = $this->getUserById($json[Strings::$SLEEP_USER_ID]);
            $sleepArray = $repository->findByUid($json[Strings::$SLEEP_USER_ID]);
            if(!$user){
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_USER,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            } else if ($sleepArray) {
                foreach ($sleepArray as $sleep) {
                    $sleepDateTime = strtotime("0:00",$sleep->getDate()->getTimestamp());
                    $jsonDateTime = strtotime("0:00",(new \DateTime($json[Strings::$SLEEP_DATE]))->getTimestamp());
                    if ($sleepDateTime == $jsonDateTime){
                        $sleep->setObject($json);
                        $em->flush();
                        $builder = new ResponseMessageBuilder(Strings::$MESSAGE_SLEEP_DATA_ALREADY_EXIST_UPDATED_INSTEAD,Strings::$STATUS_OK, (array)$sleep, Strings::$SLEEP);
                        return $builder->getResponseArray($versionRequired);
                    }
                }
            }
            $sleep = new Sleep();
            $sleep ->setObject($json);
            $em->persist($sleep);
            $em->flush();
            $builder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$sleep, Strings::$SLEEP);
            return $builder->getResponseArray($versionRequired);
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }

    /**
     * @Route("/sleep/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function updateAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $sleepJSON = $this->getParamsInContent($request,Strings::$SLEEP);
        if(empty($sleepJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if(self::isMap($sleepJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->updateSleep($sleepJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($sleepJSON as $sleep) {
            $responseMessage->addToParams($this->updateSleep($sleep, false),Strings::$SLEEP);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    private function updateSleep($json, $versionRequired){
        if ($this->requiredRequestContent(array(Strings::$SLEEP_ID,Strings::$SLEEP_USER_ID),$json)) {
            $em = $this->getDoctrine()->getManager();
            $sleep = $em->getRepository(Strings::$APP_BUNDLE_SLEEP)->find($json[Strings::$SLEEP_ID]);
            if($sleep->getUid() != $json[Strings::$SLEEP_USER_ID]){
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_INVALID_USER_ID, Strings::$STATUS_BAD_REQUEST);
                return $responseBuilder->getResponseArray($versionRequired);
            }
            if ($sleep){
                $sleep->setObject($json);
                $em->flush();
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$sleep, Strings::$SLEEP);
                return  $responseBuilder->getResponseArray($versionRequired);
            }else{
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP,Strings::$STATUS_NOT_FOUND);
                return $responseBuilder->getResponseArray($versionRequired);
            }
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }

    /**
     * @Route("/sleep/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response|void
     * @internal param $id
     */
    public function deleteAction(Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        $sleepJSON = $this->getParamsInContent($request,Strings::$SLEEP);
        if(empty($sleepJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (self::isMap($sleepJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->deleteSleep($sleepJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($sleepJSON as $sleep) {
            $responseMessage->addToParams($this->deleteSleep($sleep, false),Strings::$SLEEP);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    public function deleteSleep($json, $versionRequired){
        if ($this->requiredRequestContent(array(Strings::$SLEEP_ID,Strings::$SLEEP_USER_ID),$json)) {
            $em = $this->getDoctrine()->getManager();
            $sleep = $em->getRepository(Strings::$APP_BUNDLE_SLEEP)->find($json[Strings::$SLEEP_ID]);
            if($sleep->getUid() != $json[Strings::$SLEEP_USER_ID]){
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_INVALID_USER_ID, Strings::$STATUS_BAD_REQUEST);
                return $responseBuilder->getResponseArray($versionRequired);
            }
            if ($sleep) {
                $em->remove($sleep);
                $em->flush();
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, $sleep, Strings::$SLEEP);
                return  $responseBuilder->getResponseArray($versionRequired);
            } else {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_SLEEP,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            }
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }
}