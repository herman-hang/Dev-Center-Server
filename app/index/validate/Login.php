<?php
/**
 * 登录验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user' => 'require|length:5,15',
        'password' => 'require|length:6,15'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user.require' => '用户名不能为空',
        'password.require' => '密码不能为空',
        'user.length' => '用户名只能在5到15位之间',
        'password.length' => '密码只能在6到15位之间'
    ];
}
