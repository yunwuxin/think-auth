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

//think-auth 配置文件
use yunwuxin\auth\model\User;

return [
    'default'          => 'web',
    'guards'           => [
        'web' => [
            'type'     => 'session',
            'provider' => [
                'type'  => 'model',
                'model' => User::class,
            ],
        ],
        'api' => [
            'type'     => 'token',
            'provider' => [
                'type'  => 'model',
                'model' => User::class,
            ],
        ],
    ],
    'policy_namespace' => '\\app\\policy\\',
    'policies'         => [],
];
