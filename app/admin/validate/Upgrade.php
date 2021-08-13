<?php
/**
 * 升级中心验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

class Upgrade extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'type' => 'require',
        'version' => 'require',
        'wgt_url' => 'require',
        'way' => 'require',
        'status'=>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '升级包标题不能为空！',
        'content.require' => '升级包内容不能为空！',
        'type.require' => '请选择升级包类型！',
        'version.require' => '升级版本不能为空！',
        'wgt_url.require' => '升级包下载地址不能为空！',
        'way.require' => '请选择升级方式！',
        'status.require' => '请选择升级包状态！',
    ];

    /**
     * 发布升级包
     * @return Upgrade
     */
    public function sceneAdd()
    {
        return $this->only(['title','content','type','version','wgt_url','way','status']);
    }

    /**
     * 编辑升级包
     * @return Upgrade
     */
    public function sceneEdit()
    {
        return $this->only(['title','content','type','version','wgt_url','way']);
    }
}
