<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yunwuxin\auth\traits;

use function method_exists;
use think\helper\Str;
use yunwuxin\auth\exception\AuthorizationException;
use yunwuxin\auth\Request;

/**
 * 控制器鉴权
 * Class Authorize
 * @package yunwuxin\auth\traits
 */
class Authorize
{
    protected function authorize($action, $object = null)
    {
        $user = Request::instance()->user();

        if (!$user || !$user->can($action, $object)) {
            throw new AuthorizationException;
        }
    }

    public function __call($method, $args)
    {
        if (preg_match('/^authorize_(\w+)(?:\|([\w\\]+))?$/', $method, $match)) {

            $action = $match[1];
            $object = $match[2];
            if ($match[2] && isset($this->$match[2])) {
                $object = $this->$match[2];
            }

            $method = "authorize" . Str::studly($action);

            if (method_exists($this, $method)) {
                if (!$this->$method($object)) {
                    throw new AuthorizationException;
                }
            } else {
                $this->authorize($action, $object);
            }
        } else {
            throw new \ErrorException("Call to undefined method {$method}");
        }
    }
}