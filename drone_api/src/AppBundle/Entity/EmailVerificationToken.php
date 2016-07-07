<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 7/5/16
 * Time: 3:24 PM
 */

namespace AppBundle\Entity;
use AppBundle\Resources\Strings;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="email_verification_token")
 */
class EmailVerificationToken {


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
     * @ORM\Column(type="string", unique=true, length=100, nullable=true)
     */
    public $token;

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
     * @return EmailVerificationToken
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
     * Set emailVerificationToken
     *
     * @param  $emailVerificationToken
     *
     * @return EmailVerificationToken
     */
    public function setEmailVerificationToken($emailVerificationToken)
    {
        $this->email_verification_token = $emailVerificationToken;

        return $this;
    }

    /**
     * Get emailVerificationToken
     *
     * @return \varchar
     */
    public function getEmailVerificationToken()
    {
        return $this->email_verification_token;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return EmailVerificationToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
