<?php
/**
 * 模块公共控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\index\controller;

use app\BaseController;
use app\index\middleware\Auth;

class Base extends BaseController
{
    /**
     * 检测登录中间件调用
     * @var string[]
     */
    protected $middleware = [Auth::class];
}
