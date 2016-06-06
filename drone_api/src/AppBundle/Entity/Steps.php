<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/21/16
 * Time: 5:35 PM
 */

namespace AppBundle\Entity;
use AppBundle\Resources\Strings;

use AppBundle\Util\Date;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="steps")
 */

class Steps {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="users")
     *  @ORM\JoinColumn(name="uid", referencedColumnName="id")
     * @ORM\Column(type="integer", nullable=false)
     */
    public $uid;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $steps;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $goal;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    public $date;


    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    public $active_time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $calories;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    public $distance;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return Steps
     */
    public function setUid($userId)
    {
        $this->uid = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set steps
     *
     * @param string $steps
     *
     * @return Steps
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return integer
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return Steps
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date ? clone $date : null;
        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date ? clone $this->date : null;
    }

    /**
     * Set goal
     *
     * @param integer $goal
     *
     * @return Steps
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * Get goal
     *
     * @return integer
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * Set activeTime
     *
     * @param integer $activeTime
     *
     * @return Steps
     */
    public function setActiveTime($activeTime)
    {
        $this->active_time = $activeTime;

        return $this;
    }

    /**
     * Get activeTime
     *
     * @return integer
     */
    public function getActiveTime()
    {
        return $this->active_time;
    }

    /**
     * Set calories
     *
     * @param integer $calories
     *
     * @return Steps
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Get calories
     *
     * @return integer
     */
    public function getCalories()
    {
        return $this->calories;
    }

    /**
     * Set distance
     *
     * @param string $distance
     *
     * @return Steps
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    public function setObject($json)
    {
        // whatsup with this
        if (array_key_exists(Strings::$STEPS_STEPS,$json)) {
            $this->setSteps($json[Strings::$STEPS_STEPS]);
        }
        if (array_key_exists(Strings::$STEPS_DATE,$json)) {
            $this->setDate(new \DateTime($json[Strings::$STEPS_DATE]));
        }
        if (array_key_exists(Strings::$STEPS_USER_ID,$json)) {
            $this->setUid($json[Strings::$STEPS_USER_ID]);
        }
        if (array_key_exists(Strings::$STEPS_GOAL,$json)) {
            $this->setGoal($json[Strings::$STEPS_GOAL]);
        }
        if (array_key_exists(Strings::$STEPS_ACTIVE_TIME,$json)) {
            $this->setActiveTime($json[Strings::$STEPS_ACTIVE_TIME]);
        }
        if (array_key_exists(Strings::$STEPS_CALORIES,$json)) {
            $this->setCalories($json[Strings::$STEPS_CALORIES]);
        }
        if (array_key_exists(Strings::$STEPS_DISTANCE,$json)) {
            $this->setDistance($json[Strings::$STEPS_DISTANCE]);
        }

    }
}
