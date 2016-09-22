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

namespace think\auth\behavior;

/**
 * 权限管理
 * Class Authorization
 * @package think\auth\behavior
 */
class Authorization
{
    public function run()
    {
        if (auth()->guest()) {
            return response('Unauthorized.', 401);
        }
    }
}