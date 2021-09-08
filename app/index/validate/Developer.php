<?php
declare (strict_types=1);

namespace app\index\validate;

use think\Validate;

class Developer extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'alipay' => 'require',
        'alipay_name' => 'require',
        'wxpay' => 'require',
        'wxpay_name' => 'require',
        'qqpay' => 'require',
        'qqpay_name' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'alipay.require' => '支付宝账户不能为空！',
        'alipay_name.require' => '支付宝真实姓名不能为空！',
        'wxpay.require' => '微信账户不能为空！',
        'wxpay_name.require' => '微信真实姓名不能为空！',
        'qqpay.require' => 'QQ账户不能为空！',
        'qqpay_name.require' => 'QQ名称不能为空！',
    ];

    /**
     * 开发者申请
     * @return Developer
     */
    public function sceneBecomeDeveloper()
    {
        return $this->only(['alipay', 'alipay_name', 'wxpay', 'wxpay_name', 'qqpay', 'qqpay_name']);
    }
}
