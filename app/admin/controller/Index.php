<?php
/**
 * 后台首页控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use auth\Auth;
use think\facade\Request;

class Index extends Base
{
    public function index()
    {
        echo '您好！这是一个[admin]示例应用';
    }

    public function welcome()
    {
        echo 'Welcome';
    }
}
