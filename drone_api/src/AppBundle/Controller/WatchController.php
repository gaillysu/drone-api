<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

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

    }

    /**
     * @Route("/watch/user/{uid}?token={token}")
     * @Method({"GET"})
     * @param $uid
     * @return Response
     * @internal param $id Get all the watches from a specific user.* Get all the watches from a specific user.
     */
    public function showAction($uid){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->getStandardNotFoundResponse(Strings::$MESSAGE_ACCESS_DENIED);
        }
//        if (!$this->checkToken($token)){
//            return $this->getTokenNotRightResponse();
//        }
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
        if ($this->requiredRequestContent(array(Strings::$WATCHES_SERIAL,Strings::$WATCHES_USER_ID),$watchJSON)) {
            $foundWatch = $this->getWatchForSerial($watchJSON[Strings::$WATCHES_SERIAL]);
            if (!$this->getUserById($watchJSON[Strings::$WATCHES_USER_ID])){
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            }
            if ($foundWatch[0]){
                if($watchJSON[Strings::$WATCHES_USER_ID] != $foundWatch[0]->getUid()){
                    return $this->getResponse(Strings::$MESSAGE_WATCH_OWNED_BY_SOMEONE_ELSE,Strings::$STATUS_BAD_REQUEST);
                }else{
                    return $this->getStandard200Response($foundWatch[0],Strings::$WATCHES,Strings::$MESSAGE_WATCH_ALREADY_REGISTERED);
                }
            }
            $watch = new Watches();
            $watch ->setObject($watchJSON);
            $em = $this->getDoctrine()->getManager();
            $em->persist($watch);
            $em->flush();
            return $this->getStandard200Response($watch,Strings::$WATCHES);
        }
        return $this->getStandardMissingParamResponse();
    }

    public function updateAction(Request $request){
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
        $em = $this->getDoctrine()->getManager();
        $watchesJSON = $this->getParamsInContent($request,Strings::$WATCHES);
        if (array_key_exists(Strings::$WATCHES_ID,$watchesJSON)) {
            $foundWatch = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->find($watchesJSON[Strings::$WATCHES_ID]);
            if ($foundWatch) {
                $em->remove($foundWatch);
                $em->flush();
                return $this->getStandard200Response($foundWatch,Strings::$WATCHES,Strings::$MESSAGE_DELETED_WATCH);
            } else {
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_WATCH);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    private function getWatchForSerial($serial){
        $em = $this->getDoctrine()->getManager();
        $watch = $em->getRepository(Strings::$APP_BUNDLE_WATCHES)->findBySerial($serial);
        return $watch;
    }
}