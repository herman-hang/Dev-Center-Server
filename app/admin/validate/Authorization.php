<?php
/**
 * 授权站点验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

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
        'domain_one' => 'require',
        'status' => 'require',
        'user_id' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '名称不能为空！',
        'ip.require' => '授权IP地址不能为空！',
        'ip.ip' => '请填写有效的IP地址！',
        'domain_one.ip' => '授权域名至少填写1个！',
        'status.require' => '请选择状态！',
        'user_id.require' => '请绑定用户！',
    ];

    /**
     * 添加授权站点
     * @return Authorization
     */
    public function sceneAdd()
    {
        return $this->only(['name', 'ip', 'domain_one', 'status', 'user_id']);
    }

    /**
     * 编辑授权站点
     * @return Authorization
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'ip', 'domain_one', 'user_id']);
    }
}
