<?php
/**
 * Created by PhpStorm.
 * User: karl-john
 * Date: 8/4/2016
 * Time: 11:04 AM
 */

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class PasswordEncoder extends BasePasswordEncoder
{
     public function encodePassword($raw, $salt)
    {
        // TODO: Implement encodePassword() method.
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        // TODO: Implement isPasswordValid() method.
    }
}