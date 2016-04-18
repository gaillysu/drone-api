<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Builder\ResponseMessageBuilder;
use AppBundle\Entity\Watches;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Resources\Strings;
use Symfony\Component\HttpFoundation\Response;

class WatchController extends BasicApiController{

    /**
     * @Route("/watch")
     */
    public function indexAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        return $this->getStandard200Response(null,null,Strings::$COOl_MESSAGE, false);
    }

    /**
     * @Route("/watch/user/{uid}")
     * @Method({"GET"})
     * @param $uid
     * @return Response
     * @internal param $id Get all the watches from a specific user.* Get all the watches from a specific user.
     */
    public function showAction($uid){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_WATCHES);
            $watch = $repository->findByUid($uid);
            if ($watch) {
                return $this->getStandard200Response($watch,Strings::$WATCHES);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_WATCH);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/watch/create")
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
        $watchJSON = $this->getParamsInContent($request,Strings::$WATCHES);
        if(self::isMap($watchJSON)){
            $response = $this->getStandardResponseFormat();
            $responseMessage = $this->createWatch($watchJSON,true);
            $response->setContent(json_encode($responseMessage));
            return $response;
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($watchJSON as $watch){
            $responseMessage->addToParams($this->createWatch($watch,false),Strings::$WATCHES);
        }
        $response = $this->getStandardResponseFormat();
        $response->setContent($responseMessage->getResponseJSON(true));
        return $response;
    }

    private function createWatch($json, $versionRequired)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_WATCHES);
        if ($this->requiredRequestContent(array(Strings::$WATCHES_USER_ID, Strings::$WATCHES_SERIAL), $json)) {
            $user = $this->getUserById($json[Strings::$STEPS_USER_ID]);
            $watch = $repository->findBySerial($json[Strings::$WATCHES_USER_ID]);
            if (!$user) {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_USER,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            } else if ($watch) {
                if ($watch->getUid() == $json[Strings::$WATCHES_USER_ID]){
                    $builder = new ResponseMessageBuilder(Strings::$MESSAGE_WATCH_ALREADY_REGISTERED, Strings::$STATUS_OK, (array)$watch, Strings::$WATCHES);
                    return $builder->getResponseArray($versionRequired);
                }else{
                    $builder = new ResponseMessageBuilder(Strings::$MESSAGE_WATCH_OWNED_BY_SOMEONE_ELSE, Strings::$STATUS_BAD_REQUEST);
                    return $builder->getResponseArray($versionRequired);
                }
            }
            $watch = new Watches();
            $watch->setObject($json);
            $em->persist($watch);
            $em->flush();
            $builder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$watch, Strings::$STEPS);
            return $builder->getResponseArray($versionRequired);
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }


    /**
     * @Route("/watch/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response
     * @internal param $id
     */
    public function deleteAction(Request $request){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
        if ($this->checkTokenInRequest($request)){
            return $this->getTokenNotRightResponse();
        }
        $watchJSON = $this->getParamsInContent($request,Strings::$WATCHES);
        if (self::isMap($watchJSON)){
            $response = $this->getStandardResponseFormat();
            $responseMessage = $this->deleteSteps($watchJSON,true);
            $response->setContent(json_encode($responseMessage));
            return $response;
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($watchJSON as $step) {
            $responseMessage->addToParams($this->deleteSteps($step, false),Strings::$STEPS);
        }
        $response = $this->getStandardResponseFormat();
        $response->setContent($responseMessage->getResponseJSON(true));
        return $response;
    }

    public function deleteSteps($json, $versionRequired){
        if (array_key_exists(Strings::$WATCHES_ID,$json)) {
            $em = $this->getDoctrine()->getManager();
            $watches = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->find($json[Strings::$WATCHES_ID]);
            if ($watches) {
                $em->remove($watches);
                $em->flush();
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$watches, Strings::$WATCHES);
                return  $responseBuilder->getResponseArray($versionRequired);
            } else {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_WATCH,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            }
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }

    private function getWatchForSerial($serial){
        $em = $this->getDoctrine()->getManager();
        $watch = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->findBySerial($serial);
        return $watch;
    }
}