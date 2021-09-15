<?php
/**
 * 授权站点验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\validate;

use think\Validate;

class Authorization extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'ip' => 'require|ip',
        'domain_one' => 'require|activeUrl',
        'domain_two' => 'activeUrl',
        'domain_tree' => 'activeUrl',
        'level' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '授权站名称不能为空！',
        'ip.require' => '授权IP地址不能为空！',
        'ip.ip' => 'IP地址格式错误！',
        'domain_one.require' => '授权域名1不能为空！',
        'domain_one.activeUrl' => '授权域名1无效！',
        'domain_two.activeUrl' => '授权域名2无效！',
        'domain_tree.activeUrl' => '授权域名3无效！',
        'level.require' => '请选择授权服务！'
    ];

    /**
     * 授权站点添加
     * @return Authorization
     */
    public function sceneAdd()
    {
        return $this->only(['name', 'ip', 'domain_one', 'domain_two', 'domain_tree', 'level']);
    }

    /**
     * 授权站点编辑
     * @return Authorization
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'ip', 'domain_one', 'domain_two', 'domain_tree']);
    }

    /**
     * 授权站服务升级
     * @return Authorization
     */
    public function sceneUpdateService()
    {
        return $this->only(['upgrade_level', 'pay_type']);
    }
}
