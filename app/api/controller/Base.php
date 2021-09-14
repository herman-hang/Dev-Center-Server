<?php
/**
 * 公共控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\api\controller;

use app\api\middleware\Auth;
use app\BaseController;
use think\facade\Request;

class Base extends BaseController
{
    /**
     * KEY验证中间件调用
     * @var string[]
     */
    protected $middleware = [Auth::class];
}
