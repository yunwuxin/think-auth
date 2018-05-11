<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/5/11
 * Time: 16:53
 */

namespace yunwuxin\facade;


use think\Facade;

/**
 * Class Gate
 *
 * @package yunwuxin\facade
 * @mixin \yunwuxin\auth\Gate
 */
class Gate extends Facade
{
    protected static function getFacadeClass()
    {
        return \yunwuxin\auth\Gate::class;
    }
}