<?php
/**
 * 权限组验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Group extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'rules' => 'require',
        'status' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '权限组名称不能为空！',
        'rules.require' => '请选择权限！',
        'status.require' => '请选中状态！'
    ];

    /**
     * 权限组添加
     * @return Group
     */
    public function sceneAdd()
    {
        return $this->only(['name', 'rules']);
    }

    public function sceneEdit()
    {
        return $this->only(['status']);
    }
}
