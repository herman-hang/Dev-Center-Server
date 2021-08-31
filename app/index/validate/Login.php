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
        'password' => 'require|length:6,15',
        'passwords' => 'require|length:6,15|confirm:password',
        'email' => 'require|email'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user.require' => '用户名不能为空！',
        'password.require' => '密码不能为空！',
        'user.length' => '用户名只能在5到15位之间！',
        'user.unique' => '用户名已存在！',
        'password.length' => '密码只能在6到15位之间！',
        'passwords.require' => '确认密码不能为空！',
        'passwords.length' => '确认密码只能在6到15位之间！',
        'passwords.confirm' => '两次密码不一致！',
        'email.email' => '邮箱格式不正确！',
        'email.require' => '邮箱不能为空！'
    ];

    /**
     * 登录
     * @return Login
     */
    public function sceneLogin()
    {
        return $this->only(['user', 'password']);
    }

    /**
     * 注册
     * @return Login
     */
    public function sceneRegister()
    {
        return $this->only(['user', 'password', 'passwords'])->append('user', 'unique:user,user');
    }

    /**
     * 找回密码发送验证码表单验证
     * @return Login
     */
    public function scenePassSendCode()
    {
        return $this->only(['user', 'email']);
    }

    /**
     * 修改密码
     * @return Login
     */
    public function scenePassword()
    {
        return $this->only(['password']);
    }
}
