<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/5/11
 * Time: 14:52
 */

namespace yunwuxin\facade;


use think\Facade;

/**
 * Class Auth
 *
 * @package yunwuxin\facade
 * @mixin \yunwuxin\Auth
 */
class Auth extends Facade
{
    protected static function getFacadeClass()
    {
        return \yunwuxin\Auth::class;
    }
}