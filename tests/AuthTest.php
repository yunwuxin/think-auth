<?php

use yunwuxin\Auth;
use yunwuxin\auth\interfaces\Guard;
use yunwuxin\auth\model\User;

beforeEach(function () {
    app()->config->set([
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
    ], 'auth');

    $this->auth = app(Auth::class);
});

test('no guard', function () {
    $this->auth->guard('web2');
})->throws(InvalidArgumentException::class, 'Guard [web2] not found.');

test('guard', function () {
    $guard = $this->auth->guard('web');

    expect($guard)->toBeInstanceOf(Guard::class);
});
