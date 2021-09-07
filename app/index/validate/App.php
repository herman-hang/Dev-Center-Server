<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class App extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'img' => 'require',
        'is_pay' => 'require',
        'author' => 'require',
        'introduce' => 'require',
        'zip' => 'require',
        'type' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '名称不能为空！',
        'img.require' => '请上传缩略图！',
        'is_pay.require' => '请选择付费类型！',
        'author.require' => '作者不能为空！',
        'introduce.require' => '介绍说明不能为空！',
        'zip.require' => '下载地址不能为空！',
        'type.require' => '应用类型不能为空！'
    ];

    /**
     * 发布应用
     * @return App
     */
    public function sceneAdd()
    {
        return $this->only(['name', 'img', 'is_pay', 'author', 'introduce', 'zip', 'type']);
    }

    /**
     * 编辑应用
     * @return App
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'img', 'is_pay', 'author', 'introduce', 'zip', 'type']);
    }
}
