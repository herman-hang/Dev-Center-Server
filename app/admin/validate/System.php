<?php
/**
 * 系统管理验证器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class System extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'email'         =>  'email',
        'qq'            =>  'length:5,11|number',
        'usergroup'     =>  'length:6,9|number',
        'max_logerror'  =>  'number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'email'                 =>  '邮箱格式错误！',
        'qq.length'                =>  'QQ只能在5到11位！',
        'qq.number'             =>  'QQ号必须纯数字！',
        'usergroup.length'         =>  'QQ群号只能在6到9位！！',
        'usergroup.number'      =>  'QQ群必须纯数字！',
        'max_logerror.number'   =>  '允许登录错误次数必须是纯数字！',
    ];
}
