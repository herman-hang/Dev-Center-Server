<?php
/**
 * 广告验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Advertising extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|unique:advertising,name',
        'type' => 'require',
        'star_time' => 'require',
        'end_time' => 'require',
        'status' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '广告名称不能为空！',
        'name.unique' => '广告名称已存在！',
        'type.require' => '广告类型不能为空！',
        'star_time.require' => '广告投放时间不能为空！',
        'end_time.require' => '广告过期时间不能为空！',
        'status.require' => '广告状态不能为空！',
    ];


    /**
     * 发布广告
     * @return Advertising
     */
    public function sceneAdd()
    {
        return $this->only(['name', 'type', 'star_time', 'end_time', 'status']);
    }

    /**
     * 编辑广告
     * @return Advertising
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'type', 'star_time', 'end_time']);
    }
}
