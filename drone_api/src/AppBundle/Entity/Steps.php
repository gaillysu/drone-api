<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/21/16
 * Time: 5:35 PM
 */

namespace AppBundle\Entity;
use AppBundle\Resources\Strings;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="bigint", nullable=true)
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
     * @param integer $date
     *
     * @return Steps
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setObject($json)
    {
        // whatsup with this
        if (array_key_exists(Strings::$STEPS_STEPS,$json)) {
            $this->setSteps($json[Strings::$STEPS_STEPS]);
        }
        if (array_key_exists(Strings::$STEPS_DATE,$json)) {
            $this->setDate($json[Strings::$STEPS_DATE]);
        }
        if (array_key_exists(Strings::$STEPS_USER_ID,$json)) {
            $this->setUid($json[Strings::$STEPS_USER_ID]);
        }
    }
}
