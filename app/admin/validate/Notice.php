<?php
/**
 * 通知公告验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Notice extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'length:0,64',
        'status' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.length' => '标题只能在0到64个字符之间',
        'status.require' => '请选择状态！'
    ];

    /**
     * 发布公告
     * @return mixed
     */
    public function sceneAdd()
    {
        return $this->only(['title','status']);
    }

    /**
     * 编辑公告
     * @return Notice
     */
    public function sceneEdit()
    {
        return $this->only(['title']);
    }
}
