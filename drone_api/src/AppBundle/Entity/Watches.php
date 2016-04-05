<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/21/16
 * Time: 4:53 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Resources\Strings;

/**
 * @ORM\Entity
 * @ORM\Table(name="watches")
 */
class Watches {


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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    public $serial;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     * @return string
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return Watches
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

    public function setObject($json)
    {
        // whatsup with this
        if (array_key_exists(Strings::$WATCHES_SERIAL,$json)) {
            $this->setSerial($json[Strings::$WATCHES_SERIAL]);
        }
        if (array_key_exists(Strings::$WATCHES_USER_ID,$json)) {
            $this->setUid($json[Strings::$WATCHES_USER_ID]);
        }
    }
}
