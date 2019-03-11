<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2019/3/11
 * Time: 18:30
 */

namespace yunwuxin\auth\event;


class Login
{

    public $user;

    public $remember;

    public function __construct($user, $remember)
    {
        $this->user     = $user;
        $this->remember = $remember;
    }
}