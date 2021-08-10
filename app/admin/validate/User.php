<?php
/**
 * 用户验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user' => 'require|length:5,15|alphaNum|unique:user,user',
        'password' => 'require|length:6,15',
        'status' => 'require',
        'is_develpper' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user.require' => '用户名不能为空！',
        'user.length' => '用户名只能在5到15位之间！',
        'user.alphaNum' => '用户名只能是字母和数字组成！',
        'user.unique' => '用户名已存在！',
        'password.require' => '密码不能为空！',
        'password.length' => '密码只能在6到15位之间！',
        'status.require' => '请选择用户状态！',
        'is_develpper.require' => '请选择是否为开发者！'
    ];

    /**
     * 用户添加
     * @return User
     */
    public function sceneAdd()
    {
        return $this->only(['user','password','status','is_develpper']);
    }

    /**
     * 用户编辑
     * @return User
     */
    public function sceneEdit()
    {
        return $this->only(['user','password','is_develpper']);
    }
}
