<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

/**
 * 加密
 * @param $value
 * @return bool|string
 */
function encrypt($value)
{
    return \think\helper\Hash::make($value);
}


/**
 * @return \think\Auth
 */
function auth()
{
    return \think\Auth::instance();
}