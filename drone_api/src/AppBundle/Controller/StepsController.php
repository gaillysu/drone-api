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
use AppBundle\Entity\Steps;

class StepsController extends BasicApiController{

    /**
     * @Route("/steps")
     */
    public function indexAction(){
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
        if ($uid > -1) {
            $repository = $this->getDoctrine()->getRepository(Strings::$APP_BUNDLE_STEPS);
            $stepsArray = $repository->findByUid($uid);
            if ($stepsArray) {
                return $this->getStandard200Response($stepsArray,Strings::$STEPS);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_STEPS);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/steps/create")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function createAction(Request $request){
        $stepsJSON = $this->getParamsInContent($request,Strings::$STEPS);
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Strings::$APP_BUNDLE_STEPS);
        if ($this->requiredRequestContent(array(Strings::$STEPS_DATE,Strings::$STEPS_USER_ID,Strings::$STEPS_STEPS),$stepsJSON)) {
            $timeMidnight = strtotime("0:00",$stepsJSON[Strings::$STEPS_DATE]);
            $user = $this->getUserById($stepsJSON[Strings::$STEPS_USER_ID]);
            $stepsArray = $repository->findByUid($stepsJSON[Strings::$STEPS_USER_ID]);
            if(!$user){
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_USER);
            } else if (gmdate($timeMidnight) != gmdate($stepsJSON[Strings::$STEPS_DATE])){
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_DATE_NOT_RIGHT);
            } else if($stepsArray){
                foreach ($stepsArray as $steps){
                    if ($steps->getDate() == gmdate($timeMidnight)){
                        $steps->setObject($stepsJSON);
                        $em->flush();
                        return $this->getStandard200Response($stepsArray,Strings::$STEPS,Strings::$MESSAGE_STEPS_DATA_ALREADY_EXIST_UPDATED_INSTEAD);
                    }
                }
            }
            $steps = new Steps();
            $steps ->setObject($stepsJSON);
            $em->persist($steps);
            $em->flush();
            return $this->getStandard200Response($steps,Strings::$STEPS);
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/steps/update")
     * @Method({"PUT"})
     * @param Request $request
     * @return Response
     * @internal param $data
     */
    public function updateAction(Request $request){
        $steps = $this->getParamsInContent($request,Strings::$STEPS);
        if (array_key_exists(Strings::$STEPS_ID,$steps)) {
            $em = $this->getDoctrine()->getManager();
            $foundSteps = $em->getRepository(Strings::$APP_BUNDLE_STEPS)->find($steps[Strings::$STEPS_ID]);
            if ($foundSteps){
                $foundSteps->setObject($steps);
                $em->flush();
                return $this->getStandard200Response($steps,Strings::$STEPS);
            }else{
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_STEPS);
            }
        }
        return $this->getStandardMissingParamResponse();
    }

    /**
     * @Route("/steps/delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @return Response|void
     * @internal param $id
     */
    public function deleteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $steps = $this->getParamsInContent($request,Strings::$STEPS);
        if (array_key_exists(Strings::$STEPS_ID,$steps)) {
            $foundSteps = $em->getRepository(Strings::$APP_BUNDLE_STEPS)->find($steps[Strings::$STEPS_ID]);
            if ($foundSteps) {
                $em->remove($foundSteps);
                $em->flush();
                return $this->getStandard200Response($foundSteps,Strings::$STEPS,Strings::$MESSAGE_DELETED_STEPS);
            } else {
                return $this->getStandardNotFoundResponse(Strings::$MESSAGE_COULD_NOT_FIND_STEPS);
            }
        }
        return $this->getStandardMissingParamResponse();
    }
}