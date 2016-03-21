<?php
/**
 * Created by PhpStorm.
 * User: Karl
 * Date: 3/21/16
 * Time: 5:35 PM
 */

namespace AppBundle\Entity;

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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="users")
     *  @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @ORM\Column(type="integer")
     */
    protected $user_id;


    /**
     * @ORM\Column(type="string")
     */
    protected $steps;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $date;



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
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
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
     * @return string
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
}
