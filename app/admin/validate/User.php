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
        'passwords' => 'require|confirm:password',
        'card' => 'idCard',
        'age' => 'number|between:0,120',
        'status' => 'require',
        'sex' => 'require',
        'is_developer' => 'require',
        'email' => 'email|unique:user,email',
        'mobile' => 'mobile|unique:user,mobile',
        'qq' => 'length:5,11'
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
        'passwords.require' => '确认密码不能为空！',
        'passwords.confirm' => '两次密码不一致！',
        'card.idCard' => '身份证格式错误！',
        'status.require' => '请选择用户状态！',
        'sex.require' => '请选择性别！',
        'is_developer.require' => '请选择是否为开发者！',
        'age.between' => '年龄只能在1到120岁之间！',
        'age.number' => '年龄必须是数字！',
        'email.email' => '邮箱格式错误！',
        'email.unique' => '邮箱已存在！',
        'mobile.unique' => '手机号码已存在！',
        'mobile.mobile' => '手机号码格式错误！',
        'qq.length' => 'QQ号码只能是5到11位之间！'
    ];

    /**
     * 用户添加
     * @return User
     */
    public function sceneAdd()
    {
        return $this->only(['user', 'password', 'passwords', 'card', 'email', 'mobile', 'age', 'sex', 'status', 'is_developer', 'qq']);
    }

    /**
     * 用户编辑
     * @return User
     */
    public function sceneEdit()
    {
        return $this->only(['user', 'password', 'card', 'age', 'email', 'mobile', 'sex', 'is_developer', 'qq'])->remove('password', 'require');
    }
}
