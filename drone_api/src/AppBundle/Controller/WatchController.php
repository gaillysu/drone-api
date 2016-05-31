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
     * @Route("/watch/user/{uid}")
     * @Method({"GET"})
     * @param $uid
     * @param Request $request
     * @return Response
     * @internal param $id Get all the watches from a specific user.* Get all the watches from a specific user.
     */
    public function showAction($uid, Request $request){
        if (!$this->isAuthenticated($request)) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_WATCHES);
            $watch = $repository->findByUid($uid);
            if ($watch) {
                return ResponseFactory::makeStandard200Response($watch,Strings::$WATCHES);
            }else{
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_WATCH);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route("/watch/create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $authenticated =  $this->isAuthenticated($request);
        if($authenticated){
            return $authenticated;
        }
        $watchJSON = $this->getParamsInContent($request,Strings::$WATCHES);
        if(empty($watchJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if(self::isMap($watchJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->createWatch($watchJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($watchJSON as $watch){
            $responseMessage->addToParams($this->createWatch($watch,false),Strings::$WATCHES);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    private function createWatch($watchJSON, $versionRequired)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_WATCHES);
        if ($this->requiredRequestContent(array(Strings::$WATCHES_USER_ID, Strings::$WATCHES_SERIAL), $watchJSON)) {
            $user = $this->getUserById($watchJSON[Strings::$WATCHES_USER_ID]);
            $watch = $repository->findBySerial($watchJSON[Strings::$WATCHES_USER_ID]);
            if (!$user) {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_USER,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            } else if ($watch) {
                if ($watch->getUid() == $watchJSON[Strings::$WATCHES_USER_ID]){
                    $builder = new ResponseMessageBuilder(Strings::$MESSAGE_WATCH_ALREADY_REGISTERED, Strings::$STATUS_OK, (array)$watch, Strings::$WATCHES);
                    return $builder->getResponseArray($versionRequired);
                }else{
                    $builder = new ResponseMessageBuilder(Strings::$MESSAGE_WATCH_OWNED_BY_SOMEONE_ELSE, Strings::$STATUS_BAD_REQUEST);
                    return $builder->getResponseArray($versionRequired);
                }
            }
            $watch = new Watches();
            $watch->setObject($watchJSON);
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
        $authenticated =  $this->isAuthenticated($request);
        if(!$authenticated){
            return $authenticated;
        }
        $watchJSON = $this->getParamsInContent($request,Strings::$WATCHES);
        if(empty($watchJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (self::isMap($watchJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->deleteWatch($watchJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($watchJSON as $step) {
            $responseMessage->addToParams($this->deleteWatch($step, false),Strings::$STEPS);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    public function deleteWatch($watchJSON, $versionRequired){
        if (array_key_exists(Strings::$WATCHES_ID,$watchJSON)) {
            $em = $this->getDoctrine()->getManager();
            $watches = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->find($watchJSON[Strings::$WATCHES_ID]);
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
}