<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/22/16
 * Time: 11:40 AM
 */

namespace AppBundle\Controller;

use AppBundle\Builder\ResponseMessageBuilder;
use AppBundle\Factory\ResponseFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Resources\Strings;
use AppBundle\Entity\Steps;

class StepsController extends BasicApiController{

    /**
     * @Route("/steps")
     */
    public function indexAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        return ResponseFactory::makeCoolResponseMessage();
    }

    /**
     * @Route("/steps/user/{uid}")
     * @Method({"GET"})
     * @param int $uid
     * Get all the watches from a specific user.
     * @return Response
     * @internal param $offset
     */
    public function showAction($uid = -1){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return ResponseFactory::makeAccessDeniedResponse();
        }
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_STEPS);
            $stepsArray = $repository->findByUid($uid);

            if ($stepsArray) {
                return ResponseFactory::makeStandard200Response($stepsArray,Strings::$STEPS);
            }else{
                return ResponseFactory::makeStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_STEPS);
            }
        }
        return ResponseFactory::makeStandardMissingParamResponse();
    }

    /**
     * @Route("/steps/create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }
        $stepsJSON = $this->getParamsInContent($request,Strings::$STEPS);
        if(empty($stepsJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if(self::isMap($stepsJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->createSteps($stepsJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($stepsJSON as $steps){
            $responseMessage->addToParams($this->createSteps($steps,false),Strings::$STEPS);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    private function createSteps($json, $versionRequired)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_STEPS);
        if ($this->requiredRequestContent(array(Strings::$STEPS_DATE, Strings::$STEPS_USER_ID, Strings::$STEPS_STEPS), $json)) {
            $timeMidnight = strtotime("0:00", $json[Strings::$STEPS_DATE]);
            $user = $this->getUserById($json[Strings::$STEPS_USER_ID]);
            $stepsArray = $repository->findByUid($json[Strings::$STEPS_USER_ID]);
            if (!$user) {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_USER,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            } else if (gmdate($timeMidnight) != gmdate($json[Strings::$STEPS_DATE])) {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_DATE_NOT_RIGHT,Strings::$STATUS_BAD_REQUEST);
                return $builder->getResponseArray($versionRequired);
            } else if ($stepsArray) {
                foreach ($stepsArray as $steps) {
                    if ($steps->getDate() == gmdate($timeMidnight)) {
                        $steps->setObject($json);
                        $em->flush();
                        $builder = new ResponseMessageBuilder(Strings::$MESSAGE_STEPS_DATA_ALREADY_EXIST_UPDATED_INSTEAD,Strings::$STATUS_OK, (array)$steps, Strings::$STEPS);
                        return $builder->getResponseArray($versionRequired);
                    }
                }
            }
            $steps = new Steps();
            $steps->setObject($json);
            $em->persist($steps);
            $em->flush();
            $builder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$steps, Strings::$STEPS);
            return $builder->getResponseArray($versionRequired);
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }

    /**
     * @Route("/steps/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function updateAction(Request $request){
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }

        $stepsJSON = $this->getParamsInContent($request,Strings::$STEPS);
        if(empty($stepsJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if(self::isMap($stepsJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->updateSteps($stepsJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($stepsJSON as $step) {
            $responseMessage->addToParams($this->updateSteps($step, false),Strings::$STEPS);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));

    }

    private function updateSteps($json, $versionRequired){
        if (array_key_exists(Strings::$STEPS_ID,$json)) {
            $em = $this->getDoctrine()->getManager();
            $steps = $em->getRepository(Strings::$APP_BUNDLE_STEPS)->find($json[Strings::$STEPS_ID]);
            if ($steps){
                $steps->setObject($json);
                $em->flush();
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, (array)$steps, Strings::$STEPS);
                return  $responseBuilder->getResponseArray($versionRequired);
            }else{
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_STEPS,Strings::$STATUS_NOT_FOUND);
                return $responseBuilder->getResponseArray($versionRequired);
            }
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }

    /**
     * @Route("/steps/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response|void
     * @internal param $id
     */
    public function deleteAction(Request $request){
        $authenticated =  $this->checkAuth($request);
        if($authenticated){
            return $authenticated;
        }
        $stepsJSON = $this->getParamsInContent($request,Strings::$STEPS);
        if(empty($stepsJSON)){
            return ResponseFactory::makeEmptyOrInvalidResponse();
        }
        if (self::isMap($stepsJSON)){
            return ResponseFactory::makeStandardResponse(json_encode($this->deleteSteps($stepsJSON,true)));
        }
        $responseMessage = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK);
        foreach ($stepsJSON as $step) {
            $responseMessage->addToParams($this->deleteSteps($step, false),Strings::$STEPS);
        }
        return ResponseFactory::makeStandardResponse($responseMessage->getResponseJSON(true));
    }

    public function deleteSteps($json, $versionRequired){
        if (array_key_exists(Strings::$STEPS_ID,$json)) {
            $em = $this->getDoctrine()->getManager();
            $steps = $em->getRepository(Strings::$APP_BUNDLE_STEPS)->find($json[Strings::$STEPS_ID]);
            if ($steps) {
                $em->remove($steps);
                $em->flush();
                $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_OK,Strings::$STATUS_OK, $steps, Strings::$STEPS);
                return  $responseBuilder->getResponseArray($versionRequired);
            } else {
                $builder = new ResponseMessageBuilder(Strings::$MESSAGE_COULD_NOT_FIND_USER,Strings::$STATUS_NOT_FOUND);
                return $builder->getResponseArray($versionRequired);
            }
        }
        $responseBuilder = new ResponseMessageBuilder(Strings::$MESSAGE_MISSING_PARAMS,Strings::$STATUS_BAD_REQUEST);
        return $responseBuilder->getResponseArray($versionRequired);
    }
}