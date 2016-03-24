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
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $last_name;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $first_name;

    /**
     * @ORM\Column(type="integer", length=3, length=100, nullable=true)
     */
    protected $age;

    /**
     * Always defined in CM.
     * @ORM\Column(type="integer", length=3, length=100, nullable=true)
     */
    protected $length;

    /**
     * @ORM\Column(type="decimal", scale=8, length=100, nullable=true)
     */
    protected $last_longitude;

    /**
     * @ORM\Column(type="decimal", scale=8, length=100, nullable=true)
     */
    protected $last_latitude;


    /**
     * @ORM\OneToMany(targetEntity="Watches", mappedBy="users")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $watch_list;

    /**
     * @ORM\Column(type="string", unique=true, length=100, nullable=false)
     **/
    private $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     **/
    private $password;

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
     * Set age
     *
     * @param integer $age
     *
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
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
     * Constructor
     */
    public function __construct()
    {
        $this->watch_list = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add watchList
     *
     * @param \AppBundle\Entity\WatchList $watchList
     *
     * @return User
     */
    public function addWatchList(\AppBundle\Entity\WatchList $watchList)
    {
        $this->watch_list[] = $watchList;

        return $this;
    }

    /**
     * Remove watchList
     *
     * @param \AppBundle\Entity\WatchList $watchList
     */
    public function removeWatchList(\AppBundle\Entity\WatchList $watchList)
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
        if (array_key_exists(Strings::$USER_AGE,$json)) {
            $this->setFirstName($json[Strings::$USER_AGE]);
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
}
