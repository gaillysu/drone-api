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
 * @ORM\Table(name="sleep")
 */

class Sleep {

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
    public $wake_time;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $light_sleep;


    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $deep_sleep;


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
     * Set uid
     *
     * @param integer $uid
     *
     * @return Sleep
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set wakeTime
     *
     * @param string $wakeTime
     *
     * @return Sleep
     */
    public function setWakeTime($wakeTime)
    {
        $this->wake_time = $wakeTime;

        return $this;
    }

    /**
     * Get wakeTime
     *
     * @return string
     */
    public function getWakeTime()
    {
        return $this->wake_time;
    }

    /**
     * Set lightSleep
     *
     * @param string $lightSleep
     *
     * @return Sleep
     */
    public function setLightSleep($lightSleep)
    {
        $this->light_sleep = $lightSleep;

        return $this;
    }

    /**
     * Get lightSleep
     *
     * @return string
     */
    public function getLightSleep()
    {
        return $this->light_sleep;
    }

    /**
     * Set deepSleep
     *
     * @param string $deepSleep
     *
     * @return Sleep
     */
    public function setDeepSleep($deepSleep)
    {
        $this->deep_sleep = $deepSleep;

        return $this;
    }

    /**
     * Get deepSleep
     *
     * @return string
     */
    public function getDeepSleep()
    {
        return $this->deep_sleep;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return Sleep
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
        if (array_key_exists(Strings::$SLEEP_USER_ID,$json)) {
            $this->setUid($json[Strings::$SLEEP_USER_ID]);
        }
        if (array_key_exists(Strings::$SLEEP_DEEP_SLEEP,$json)) {
            $this->setDeepSleep($json[Strings::$SLEEP_DEEP_SLEEP]);
        }
        if (array_key_exists(Strings::$SLEEP_LIGHT_SLEEP,$json)) {
            $this->setLightSleep($json[Strings::$SLEEP_LIGHT_SLEEP]);
        }
        if (array_key_exists(Strings::$SLEEP_WAKE_TIME,$json)) {
            $this->setWakeTime($json[Strings::$SLEEP_WAKE_TIME]);
        }

        if (array_key_exists(Strings::$SLEEP_DATE,$json)) {
            $this->setDate($json[Strings::$SLEEP_DATE]);
        }
    }
}
