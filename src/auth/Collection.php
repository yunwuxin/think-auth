<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/8/6
 * Time: 14:47
 */

namespace yunwuxin\auth;


use yunwuxin\auth\traits\PoliciesCollection;

class Collection extends \think\model\Collection
{
    use PoliciesCollection;
}