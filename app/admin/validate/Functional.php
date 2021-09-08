<?php
/**
 * 功能配置验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Functional extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'alipay_type' => 'require',
        'sms_type' => 'require',
        'bind_mobile'=>'number',
        'relieve_mobile'=>'number'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'alipay_type.require' => '请选择支付宝支付接口类型！',
        'sms_type.require' => '请选择短信接口类型！',
        'bind_mobile.number'=>'绑定手机号码模板ID必须是数字！',
        'relieve_mobile.number'=>'解除手机号码模板ID必须是数字！'
    ];

    /**
     * 编辑支付
     * @return Functional
     */
    public function scenePayEdit()
    {
        return $this->only(['alipay_type']);
    }

    /**
     * 编辑短信
     * @return Functional
     */
    public function sceneSmsEdit()
    {
        return $this->only(['sms_type','bind_mobile','relieve_mobile']);
    }
}
