<?php
/**
 * 功能配置验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

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
        'level' => 'require',
        'brokerage' => 'require|number'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'level.require' => '请选择开发者等级！',
        'brokerage.require' => '服务费不能为空！',
        'brokerage.number' => '服务费必须是数字！'
    ];

    public function sceneEdit()
    {
        return $this->only(['level', 'brokerage']);
    }
}
