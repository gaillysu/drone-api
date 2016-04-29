<?php

namespace AppBundle\Entity;

use AppBundle\Resources\Strings;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class Users{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $last_name;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    public $first_name;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    public $birthday;

    /**
     * Always defined in KG.
     * @ORM\Column(type="integer", length=3, length=100, nullable=true)
     */
    public $weight;

    /**
     * Always defined in CM.
     * @ORM\Column(type="integer", length=3, length=100, nullable=true)
     */
    public $length;

    /**
     * 1 is male 0 is female
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    public $sex;

    /**
     * @ORM\Column(type="decimal", scale=8, length=100, nullable=true)
     */
    public $last_longitude;

    /**
     * @ORM\Column(type="decimal", scale=8, length=100, nullable=true)
     */
    public $last_latitude;


    /**
     * @ORM\OneToMany(targetEntity="Watches", mappedBy="users")
     * @ORM\JoinColumn(name="id", referencedColumnName="uid")
     */
    public $watch_list;

    /**
     * @ORM\Column(type="string", unique=true, length=100, nullable=false)
     **/
    public $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     **/
    public $password;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->watch_list = new \Doctrine\Common\Collections\ArrayCollection();
        $this->last_latitude = 0;
        $this->last_longitude = 0;
    }

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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set length
     *
     * @param integer $length
     *
     * @return User
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set lastLongitude
     *
     * @param string $lastLongitude
     *
     * @return User
     */
    public function setLastLongitude($lastLongitude)
    {
        $this->last_longitude = $lastLongitude;

        return $this;
    }

    /**
     * Get lastLongitude
     *
     * @return string
     */
    public function getLastLongitude()
    {
        return $this->last_longitude;
    }

    /**
     * Set lastLatitude
     *
     * @param string $lastLatitude
     *
     * @return User
     */
    public function setLastLatitude($lastLatitude)
    {
        $this->last_latitude = $lastLatitude;

        return $this;
    }

    /**
     * Get lastLatitude
     *
     * @return string
     */
    public function getLastLatitude()
    {
        return $this->last_latitude;
    }

    /**
     * Set watchList
     *
     * @param integer $watchList
     *
     * @return User
     */
    public function setWatchList($watchList)
    {
        $this->watch_list = $watchList;

        return $this;
    }

    /**
     * Get watchList
     *
     * @return integer
     */
    public function getWatchList()
    {
        return $this->watch_list;
    }


    /**
     * Add watchList
     *
     * @param Watches|WatchList $watchList
     * @return User
     */
    public function addWatchList(Watches $watchList)
    {
        $this->watch_list[] = $watchList;

        return $this;
    }

    /**
     * Remove watchList
     *
     * @param Watches|WatchList $watchList
     */
    public function removeWatchList(Watches $watchList)
    {
        $this->watch_list->removeElement($watchList);
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setObject($json){
        // whatsup with this
        if (array_key_exists(Strings::$USER_EMAIL,$json)) {
            $this->setEmail($json[Strings::$USER_EMAIL]);
        }
        if (array_key_exists(Strings::$USER_PASSWORD,$json)) {
            $this->setPassword($json[Strings::$USER_PASSWORD]);
        }
        if (array_key_exists(Strings::$USER_FIRST_NAME,$json)) {
            $this->setFirstName($json[Strings::$USER_FIRST_NAME]);
        }
        if (array_key_exists(Strings::$USER_BIRTHDAY,$json)) {
            $this->setBirthday(new \DateTime($json[Strings::$USER_BIRTHDAY]));
        }
        if (array_key_exists(Strings::$USER_WEIGHT,$json)) {
            $this->setWeight($json[Strings::$USER_WEIGHT]);
        }
        if (array_key_exists(Strings::$USER_SEX,$json)) {
            $this->setSex($json[Strings::$USER_SEX]);
        }
        if (array_key_exists(Strings::$USER_LAST_NAME,$json)) {
            $this->setLastName($json[Strings::$USER_LAST_NAME]);
        }
        if (array_key_exists(Strings::$USER_LAST_LONGITUDE,$json)) {
            $this->setLastLongitude($json[Strings::$USER_LAST_LONGITUDE]);
        }
        if (array_key_exists(Strings::$USER_LAST_LATITUDE,$json)) {
            $this->setLastLatitude($json[Strings::$USER_LAST_LATITUDE]);
        }
        if (array_key_exists(Strings::$USER_LENGTH,$json)) {
            $this->setLength($json[Strings::$USER_LENGTH]);
        }
    }


    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Users
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Users
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     *
     * @return Users
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer
     */
    public function getSex()
    {
        return $this->sex;
    }
}
