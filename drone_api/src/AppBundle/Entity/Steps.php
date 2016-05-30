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
     * @ORM\Column(type="integer", nullable=false)
     */
    public $steps;


    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    public $goal;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    public $date;

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
     * @param integer $steps
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
        if (array_key_exists(Strings::$STEPS_DATE,$json)) {
            $this->setDate($json[Strings::$STEPS_DATE]);
        }
    }
}
