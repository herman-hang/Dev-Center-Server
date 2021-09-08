<?php
/**
 * 账号管理验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\validate;

use think\facade\Request;
use think\Validate;

class Account extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'age' => 'number|between:0,120',
        'sex' => 'require',
        'qq' => 'length:5,11',
        'mobile' => 'require|mobile|unique:user,mobile'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'age.number' => '年龄只能是数字！',
        'age.between' => '年龄只能是0到120岁之间！',
        'sex.require' => '性别不能为空！',
        'qq.length' => 'QQ号码只能是5到11位之间',
        'mobile.require' => '手机号码不能为空！',
        'mobile.unique' => '手机号码已存在！',
        'mobile.mobile' => '手机号码格式错误！',
    ];

    /**
     * 我的资料编辑
     * @return Account
     */
    public function sceneMaterialEdit()
    {
        return $this->only(['age', 'sex', 'qq']);
    }

    /**
     * 绑定/解除手机号码
     * @return Account
     */
    public function sceneBindMobile()
    {
        return $this->only(['mobile']);
    }
}
