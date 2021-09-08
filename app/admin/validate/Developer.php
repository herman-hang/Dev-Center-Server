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
        'brokerage' => 'require|number',
        'copper' => 'require|number',
        'silver' => 'require|number',
        'gold' => 'require|number',
        'copper_silver' => 'require|number',
        'silver_gold' => 'require|number'
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
        'brokerage.number' => '服务费必须是数字！',
        'copper.require' => '铜牌服务费率不能为空！',
        'copper.number' => '铜牌服务费率必须是数字！',
        'silver.require' => '银牌服务费率不能为空！',
        'silver.number' => '银牌服务费率必须是数字！',
        'gold.require' => '金牌服务费率不能为空！',
        'gold.number' => '金牌服务费率必须是数字！',
        'copper_silver.require' => '铜牌升级到银牌不能为空！',
        'copper_silver.number' => '铜牌升级到银牌必须是数字！',
        'silver_copper.require' => '银牌升级到金牌不能为空！',
        'silver_gold.number' => '银牌升级到金牌必须是数字！',
    ];

    /**
     * 开发者编辑
     * @return Developer
     */
    public function sceneEdit()
    {
        return $this->only(['level', 'brokerage']);
    }

    /**
     * 开发者配置编辑
     * @return Developer
     */
    public function sceneDeveloperConfigEdit()
    {
        return $this->only(['copper', 'silver', 'gold', 'copper_silver', 'silver_gold']);
    }
}
