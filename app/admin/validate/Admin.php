<?php
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user' => 'require|length:5,15|alphaNum|unique:admin',
        'name' => 'chs',
        'password' => 'require|length:6,15',
        'passwords' => 'require|confirm:password',
        'mobile' => 'mobile|unique:admin',
        'email' => 'email|unique:admin',
        'age' => 'number|between:1,120',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user.require' => '账号不能为空！',
        'user.length' => '账号只能在5到15位之间！',
        'user.alphaNum' => '账号只能是字母和数字组成！',
        'user.unique' => '账号已存在！',
        'name.chs' => '真实姓名只能是汉字！',
        'password.require' => '密码不能为空！',
        'password.length' => '密码只能在6到15位之间！',
        'passwords.require' => '确认密码不能为空！',
        'passwords.confirm' => '两次密码不一致！',
        'mobile.mobile' => '手机号码格式不正确！',
        'mobile.unique' => '手机号码已存在！',
        'email.email' => '邮箱格式不正确！',
        'email.unique' => '邮箱已存在！',
        'age.number' => '年龄必须是数字！',
        'age.between' => '年龄只能在1-120岁之间！',
        'age.require' => '年龄不能为空！',
    ];

    // edit 验证场景定义
    public function scenepassEdit()
    {
        return $this->only(['password','passwords']);
    }
}
