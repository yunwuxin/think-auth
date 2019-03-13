<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2019/3/12
 * Time: 17:55
 */

namespace yunwuxin\auth;

class Service extends \think\Service
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../route.php');
    }
}